const fs = require("fs");
const path = require("path");
const pool = require("../config/database");
require("dotenv").config();

const ROUTES_DIR = path.join(__dirname, "..", "routes");
const DB_NAME = process.env.DB_NAME || "klinikmedishewan";

function readRouteFiles() {
  const files = fs.readdirSync(ROUTES_DIR).filter((f) => f.endsWith(".js"));
  return files.map((f) => ({ name: f, content: fs.readFileSync(path.join(ROUTES_DIR, f), "utf8") }));
}

function extractExpectedSchema(files) {
  const tables = {};
  const tableNameRe = /\b(?:FROM|JOIN|INSERT INTO|UPDATE|INTO)\s+([A-Za-z0-9_]+)/gi;
  const insertColsRe = /INSERT\s+INTO\s+([A-Za-z0-9_]+)\s*\(([^)]+)\)/gi;
  const updateColsRe = /UPDATE\s+([A-Za-z0-9_]+)\s+SET\s+([^;]+)/gi;
  const tableDotColRe = /\b([A-Za-z0-9_]+)\.([A-Za-z0-9_]+)\b/g;
  const selectColsRe = /SELECT\s+([\s\S]*?)\s+FROM/gi;

  files.forEach((f) => {
    const src = f.content;

    // table names
    let m;
    while ((m = tableNameRe.exec(src))) {
      const t = m[1];
      tables[t] = tables[t] || new Set();
    }

    // INSERT columns
    while ((m = insertColsRe.exec(src))) {
      const t = m[1];
      const cols = m[2].split(",").map((s) => s.trim().replace(/[`"']/g, ""));
      tables[t] = tables[t] || new Set();
      cols.forEach((c) => {
        if (c) tables[t].add(c);
      });
    }

    // UPDATE columns
    while ((m = updateColsRe.exec(src))) {
      const t = m[1];
      const cols = m[2].split(",").map((p) => p.split("=")[0].trim().replace(/[`"']/g, ""));
      tables[t] = tables[t] || new Set();
      cols.forEach((c) => {
        if (c) tables[t].add(c);
      });
    }

    // table.column occurrences
    while ((m = tableDotColRe.exec(src))) {
      const t = m[1];
      const c = m[2];
      tables[t] = tables[t] || new Set();
      tables[t].add(c);
    }

    // SELECT columns heuristics
    while ((m = selectColsRe.exec(src))) {
      const colsPart = m[1];
      const cols = colsPart.split(",").map((s) => s.trim());
      cols.forEach((col) => {
        // if format table.col
        const td = col.match(/([A-Za-z0-9_]+)\.([A-Za-z0-9_]+)/);
        if (td) {
          tables[td[1]] = tables[td[1]] || new Set();
          tables[td[1]].add(td[2]);
        }
      });
    }
  });

  // convert sets to arrays
  const out = {};
  Object.keys(tables).forEach((t) => {
    out[t] = Array.from(tables[t]).sort();
  });
  return out;
}

async function getActualSchema() {
  const conn = await pool.getConnection();
  const [rows] = await conn.query(`SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, ORDINAL_POSITION`, [DB_NAME]);
  conn.release();

  const actual = {};
  rows.forEach((r) => {
    actual[r.TABLE_NAME] = actual[r.TABLE_NAME] || [];
    actual[r.TABLE_NAME].push(r.COLUMN_NAME);
  });
  return actual;
}

function compare(expected, actual) {
  const report = {};
  const allTables = new Set([...Object.keys(expected), ...Object.keys(actual)]);
  allTables.forEach((t) => {
    const exp = new Set(expected[t] || []);
    const act = new Set(actual[t] || []);
    const missingInDB = [...exp].filter((c) => !act.has(c));
    const extraInDB = [...act].filter((c) => !exp.has(c));
    report[t] = { expected: [...exp].sort(), actual: [...act].sort(), missingInDB, extraInDB };
  });
  return report;
}

(async () => {
  try {
    console.log("Reading route files...");
    const files = readRouteFiles();
    const expected = extractExpectedSchema(files);

    console.log("Querying database INFORMATION_SCHEMA...");
    const actual = await getActualSchema();

    // Keep only routes' expected columns for tables that actually exist in DB
    const actualTables = Object.keys(actual);
    const filteredExpected = {};
    actualTables.forEach((t) => {
      // sanitize expected column names: keep only simple identifiers
      const raw = expected[t] || [];
      filteredExpected[t] = raw.filter((c) => /^[A-Za-z0-9_]+$/.test(c));
    });

    const report = compare(filteredExpected, actual);
    console.log(JSON.stringify(report, null, 2));

    // Print a short summary
    Object.keys(report).forEach((t) => {
      const r = report[t];
      if (r.missingInDB.length || r.extraInDB.length) {
        console.log(`\nTable: ${t}`);
        if (r.missingInDB.length) console.log("  Missing in DB:", r.missingInDB.join(", "));
        if (r.extraInDB.length) console.log("  Extra in DB:", r.extraInDB.join(", "));
      }
    });

    process.exit(0);
  } catch (err) {
    console.error("Error:", err.message);
    process.exit(2);
  }
})();
