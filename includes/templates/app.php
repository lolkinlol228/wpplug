<?php if (!defined("ABSPATH")) exit; ?>
<style>


:root {
  --bg: #fafbfc;
  --surface: #ffffff;
  --surface2: #f6f8fa;
  --border: #e1e4e8;
  --border-dark: #d0d7de;
  --text: #24292f;
  --text-secondary: #57606a;
  --text-tertiary: #8c959f;
  --primary: #0969da;
  --primary-hover: #0550ae;
  --danger: #cf222e;
  --success: #1a7f37;
  --warning: #9a6700;
  --sunday: #cf222e;
  --holiday: #9a6700;
  --sick: #0969da;
  --shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
  --shadow-md: 0 3px 6px rgba(0,0,0,0.08), 0 3px 6px rgba(0,0,0,0.1);
  --radius: 6px;
}

.tabel-app-root, .tabel-app-root * { margin:0; padding:0; box-sizing:border-box; }

body.tabel-ucheta-page, .tabel-app-root {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  line-height: 1.5;
}

/* ─── Scrollbar ─── */
::-webkit-scrollbar { width:8px; height:8px; }
::-webkit-scrollbar-track { background:var(--surface2); }
::-webkit-scrollbar-thumb { background:var(--border-dark); border-radius:4px; }
::-webkit-scrollbar-thumb:hover { background:var(--text-tertiary); }

/* ─── Layout ─── */
.app-container {
  display: flex;
  min-height: 100vh;
}

/* ─── Sidebar ─── */
.sidebar {
  width: 290px;
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid var(--border);
}

.sidebar-header h1 {
  font-size: 16px;
  font-weight: 600;
  color: var(--text);
  letter-spacing: -0.2px;
}

.sidebar-section {
  padding: 16px;
  border-bottom: 1px solid var(--border);
}

.sidebar-section h3 {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-tertiary);
  margin-bottom: 10px;
  font-weight: 600;
}

.nav-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 8px 12px;
  background: transparent;
  border: none;
  border-radius: var(--radius);
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
}
.nav-btn:hover { background: var(--surface2); color: var(--text); }
.nav-btn.active { background: var(--primary); color: #fff; }

.nav-btn svg { width:16px; height:16px; flex-shrink:0; }

/* ─── Lang switcher ─── */
.lang-row {
  display: flex;
  gap: 4px;
  padding: 4px 0;
}
.lang-btn {
  flex: 1;
  padding: 6px;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  background: transparent;
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.lang-btn:hover { border-color: var(--border-dark); background: var(--surface2); }
.lang-btn.active { background: var(--text); border-color: var(--text); color: #fff; }

/* ─── Month selector ─── */
.month-selector {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}
.month-selector select, .month-selector input {
  padding: 6px 10px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  color: var(--text);
  font-family: inherit;
  font-size: 13px;
  outline: none;
  transition: border-color 0.15s;
}
.month-selector select:focus, .month-selector input:focus {
  border-color: var(--primary);
}
.quick-btn {
  padding: 5px 10px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.quick-btn:hover { background: var(--surface2); border-color: var(--border-dark); }

/* ─── Main Content ─── */
.main-content {
  flex: 1;
  padding: 32px 40px;
  overflow-x: auto;  
  user-select: none;
}


.page { display: none; }
.page.active { display: block; }

.page-title {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
  color: var(--text);
  letter-spacing: -0.3px;
}

/* ─── Employees Page ─── */
.emp-form {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 20px;
  margin-bottom: 20px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 14px;
  align-items: end;
}

.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-secondary);
}
.form-group input, .form-group select {
  padding: 8px 12px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  color: var(--text);
  font-family: inherit;
  font-size: 14px;
  outline: none;
  transition: border-color 0.15s;
}
.form-group input:focus, .form-group select:focus {
  border-color: var(--primary);
}
.form-group input::placeholder { color: var(--text-tertiary); }

.btn {
  padding: 8px 16px;
  border-radius: var(--radius);
  border: none;
  font-family: inherit;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.btn-primary { background: var(--primary); color: #fff; }
.btn-primary:hover { background: var(--primary-hover); }
.btn-danger { background: var(--danger); color: #fff; }
.btn-danger:hover { opacity: 0.9; }
.btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--text-secondary); }
.btn-ghost:hover { border-color: var(--border-dark); background: var(--surface2); }
.btn-success { background: var(--success); color: #fff; }
.btn-success:hover { opacity: 0.9; }

/* ─── Employees List ─── */
.emp-grid {
  display: grid;
  gap: 10px;
}

.emp-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.emp-card:hover { border-color: var(--border-dark); box-shadow: var(--shadow); }

.emp-info { display: flex; flex-direction: column; gap: 4px; }
.emp-name { font-weight: 600; font-size: 15px; }
.emp-pos { color: var(--text-secondary); font-size: 13px; }
.emp-meta {
  display: flex;
  gap: 10px;
  margin-top: 4px;
}
.emp-badge {
  padding: 2px 8px;
  border-radius: 3px;
  font-size: 11px;
  font-weight: 600;
  border: 1px solid;
}
.badge-rate { background: #ddf4ff; color: #0969da; border-color: #54aeff; }
.badge-fixed { background: #dafbe1; color: #1a7f37; border-color: #4ac26b; }

.emp-actions { display: flex; gap: 6px; }
.emp-actions .btn { padding: 6px 12px; font-size: 13px; }

/* ─── Timesheet ─── */
.timesheet-controls {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.ts-table-wrap {
  overflow-x: auto;
  border-radius: var(--radius);
  border: 1px solid var(--border);
  margin-bottom: 20px;
  background: var(--surface);
}

.ts-table {
  width: max-content;
  min-width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}

.ts-table th {
  background: var(--surface2);
  padding: 10px 12px;
  font-weight: 600;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: var(--text-secondary);
  border: 1px solid var(--border);
  position: sticky;
  top: 0;
  z-index: 2;
  white-space: nowrap;
}

.ts-table th.day-header {
  min-width: 50px;
  text-align: center;
  font-size: 10px;
}
.ts-table th.day-header.sunday {
  color: var(--sunday);
  background: #fff1f3;
}

.ts-table td {
  padding: 8px 10px;
  border: 1px solid var(--border);
  text-align: center;
  white-space: nowrap;
  font-family: 'JetBrains Mono', monospace;
  font-size: 12px;
}

.ts-table td.emp-cell {
  text-align: left;
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  background: var(--surface);
  position: sticky;
  left: 0;
  z-index: 1;
  min-width: 160px;
  border-right: 2px solid var(--border-dark);
}

.ts-table td.pos-cell {
  text-align: left;
  font-family: 'Inter', sans-serif;
  color: var(--text-secondary);
  font-size: 12px;
}

.ts-table td.pos-cell[contenteditable="true"],
.ts-table td.rate-cell[contenteditable="true"] {
  position: relative;
}

.ts-table td.pos-cell.has-custom::after,
.ts-table td.rate-cell.has-custom::after {
  content: '●';
  position: absolute;
  top: 2px;
  right: 4px;
  font-size: 8px;
  color: var(--primary);
}

.ts-table td.pos-cell[contenteditable="true"]:hover,
.ts-table td.rate-cell[contenteditable="true"]:hover {
  background: var(--surface2);
  outline: 1px solid var(--primary);
}

.ts-table td.pos-cell[contenteditable="true"]:focus,
.ts-table td.rate-cell[contenteditable="true"]:focus {
  background: #fff;
  outline: 2px solid var(--primary);
  z-index: 10;
}

.ts-table td.rate-cell {
  font-weight: 600;
  color: var(--primary);
}

.day-cell {
  cursor: pointer;
  transition: all 0.1s;
  min-width: 50px;
  position: relative;
}
.day-cell:hover { background: var(--surface2) !important; }

.day-cell.status-work { background: #dafbe1; color: var(--success); font-weight: 500; }
.day-cell.status-day_off { background: #fff1f3; color: var(--sunday); }
.day-cell.status-holiday { background: #fff8dc; color: var(--warning); }
.day-cell.status-sick { background: #ddf4ff; color: var(--sick); }
.day-cell.status-absent { background: var(--surface2); color: var(--text-tertiary); }
.day-cell.status-vacation { background: #f3e5f5; color: #9C27B0; font-weight: 600; }
.day-cell.status-business_trip { background: #fff3e0; color: #E65100; font-weight: 600; }
.day-cell.status-maternity { background: #fce4ec; color: #C2185B; font-weight: 600; }
.day-cell.status-childcare { background: #fbe9e7; color: #BF360C; font-weight: 600; }
.day-cell.status-study_leave { background: #eceff1; color: #455A64; font-weight: 600; }
.day-cell.status-admin_leave { background: #efebe9; color: #4E342E; font-weight: 600; }
.day-cell.status-absence { background: #ffebee; color: #C62828; font-weight: 600; }
.day-cell.status-late { background: #fff8e1; color: #E65100; font-weight: 600; }
.day-cell.status-early_leave { background: #fbe9e7; color: #BF360C; font-weight: 600; }

/* Editable name cell */
.ts-table td.name-cell-editable {
  text-align: left;
  font-family: 'Inter', sans-serif;
  font-weight: 500;
  background: var(--surface);
  position: sticky;
  left: 0;
  z-index: 1;
  min-width: 160px;
  border-right: 2px solid var(--border-dark);
  cursor: text;
}
.ts-table td.name-cell-editable:hover { background: var(--surface2); outline: 1px solid var(--primary); }
.ts-table td.name-cell-editable:focus { background: #fff; outline: 2px solid var(--primary); z-index: 10; }

.summary-cell {
  font-weight: 600;
  color: var(--text);
  background: var(--surface2);
}

/* ─── Summary Tables ─── */
.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 12px;
  margin-top: 20px;
}

.summary-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 16px;
}
.summary-card h4 {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: var(--text-tertiary);
  margin-bottom: 8px;
  font-weight: 600;
}
.summary-card .big-num {
  font-size: 26px;
  font-weight: 600;
  font-family: 'JetBrains Mono', monospace;
  color: var(--text);
}
/* ─── Excel Settings Page ─── */
.es-layout {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  align-items: flex-start;
}

.es-panel {
  flex: 1;
  min-width: 340px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.es-panel-title {
  font-weight: 600;
  font-size: 14px;
  color: var(--primary);
  margin-bottom: 14px;
  border-bottom: 1px solid var(--border);
  padding-bottom: 10px;
}

.es-section-title {
  font-weight: 600;
  font-size: 13px;
  color: var(--text);
  margin: 16px 0 10px;
}

.es-cols-header {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 8px;
  margin-bottom: 8px;
}

.es-cols-header span {
  font-size: 11px;
  font-weight: 600;
  color: var(--text-tertiary);
  text-align: center;
  background: var(--surface2);
  padding: 4px 6px;
  border-radius: 4px;
}

.es-row3 {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 10px;
}

.es-sigs {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.es-actions {
  margin-top: 16px;
}

/* Mark rows in settings */
.es-mark-row {
  display: grid;
  grid-template-columns: 80px 1fr 2fr 40px;
  gap: 8px;
  align-items: end;
  padding: 8px 0;
  border-bottom: 1px solid var(--border);
}
.es-mark-row:last-of-type { border-bottom: none; }
.es-mark-sym input { text-align: center; font-weight: 700; font-size: 15px; }
.es-mark-del .btn { padding: 7px 10px; }

/* ─── Dropdown for day cells ─── */
.cell-dropdown {
  position: fixed;
  z-index: 1000;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  padding: 6px;
  min-width: 160px;
  display: none;
  max-height: 70vh;
  overflow-y: auto;
  overflow-x: hidden;
}
.cell-dropdown.show { display: block; }
.cell-dropdown::-webkit-scrollbar { width: 5px; }
.cell-dropdown::-webkit-scrollbar-track { background: transparent; }
.cell-dropdown::-webkit-scrollbar-thumb { background: var(--border-dark); border-radius: 3px; }
.cell-dropdown .custom-input-row {
  position: sticky;
  bottom: 0;
  background: var(--surface);
}
.cell-dropdown button {
  display: block;
  width: 100%;
  padding: 8px 12px;
  background: transparent;
  border: none;
  border-radius: 4px;
  color: var(--text);
  font-family: inherit;
  font-size: 13px;
  text-align: left;
  cursor: pointer;
  transition: background 0.1s;
  font-weight: 500;
}
.cell-dropdown button:hover { background: var(--surface2); }
.cell-dropdown .dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 8px;
}
.dot-work { background: var(--success); }
.dot-day_off { background: var(--sunday); }
.dot-holiday { background: var(--warning); }
.dot-sick { background: var(--sick); }
.dot-absent { background: var(--text-tertiary); }

.cell-dropdown .custom-input-row {
  display: flex;
  gap: 4px;
  padding: 6px;
  border-top: 1px solid var(--border);
  margin-top: 4px;
}
.cell-dropdown .custom-input-row input {
  flex: 1;
  padding: 6px 8px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 4px;
  color: var(--text);
  font-family: inherit;
  font-size: 12px;
  outline: none;
}
.cell-dropdown .custom-input-row button {
  width: auto;
  padding: 6px 12px;
  background: var(--primary);
  color: #fff;
  border-radius: 4px;
  font-weight: 600;
  font-size: 12px;
}

/* ─── Autocomplete ─── */
.autocomplete-list {
  position: absolute;
  z-index: 100;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  max-height: 200px;
  overflow-y: auto;
  display: none;
}
.autocomplete-list.show { display: block; }
.autocomplete-item {
  padding: 10px 12px;
  cursor: pointer;
  font-size: 13px;
  transition: background 0.1s;
}
.autocomplete-item:hover { background: var(--surface2); }
.autocomplete-item small { color: var(--text-tertiary); margin-left: 6px; }

/* ─── Tooltip ─── */
[data-tooltip] {
  position: relative;
}
[data-tooltip]:hover::after {
  content: attr(data-tooltip);
  position: absolute;
  bottom: calc(100% + 6px);
  left: 50%;
  transform: translateX(-50%);
  background: var(--text);
  color: #fff;
  padding: 6px 10px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 500;
  white-space: nowrap;
  z-index: 999;
  pointer-events: none;
  box-shadow: var(--shadow-md);
}

/* ─── Export Dropdown ─── */
.export-menu {
  position: relative;
  display: inline-block;
}
.export-dropdown {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 4px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  padding: 6px;
  min-width: 200px;
  z-index: 50;
}
.export-dropdown.show { display: block; }
.export-dropdown a, .export-dropdown button {
  display: block;
  width: 100%;
  padding: 8px 12px;
  background: transparent;
  border: none;
  border-radius: 4px;
  color: var(--text);
  font-family: inherit;
  font-size: 13px;
  text-align: left;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.1s;
  font-weight: 500;
}
.export-dropdown a:hover, .export-dropdown button:hover { background: var(--surface2); }

/* ─── Empty state ─── */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-tertiary);
}
.empty-state svg { width: 48px; height: 48px; margin-bottom: 12px; opacity: 0.3; }
.empty-state p { font-size: 15px; }

/* ─── Responsive ─── */
@media (max-width: 900px) {
  .sidebar { width: 240px; }
  .main-content { padding: 20px; }
}
@media (max-width: 700px) {
  .app-container { flex-direction: column; }
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
    flex-direction: row;
    flex-wrap: wrap;
    overflow-x: auto;
  }
  .sidebar-header { padding: 12px 16px; }
  .sidebar-section { padding: 8px 12px; }
}

/* ─── Animations ─── */
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
.emp-card, .summary-card { animation: fadeIn 0.2s ease; }

/* ─── User bar ─── */
.user-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border-top: 1px solid var(--border);
  margin-top: auto;
  background: var(--surface2);
}
.user-bar .user-avatar {
  width: 30px; height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0969da, #1a7f37);
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700; color: #fff;
  flex-shrink: 0;
}
.user-bar .user-name { font-size: 13px; font-weight: 600; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-bar .user-role { font-size: 10px; color: var(--text-tertiary); }
.user-bar .btn-logout {
  padding: 4px 8px; font-size: 11px; font-weight: 600;
  background: transparent; border: 1px solid var(--border);
  border-radius: 4px; color: var(--text-secondary); cursor: pointer;
  transition: all 0.15s;
}
.user-bar .btn-logout:hover { background: #ffebe9; border-color: var(--danger); color: var(--danger); }

/* ─── User management page ─── */
.user-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 10px;
  transition: border-color 0.15s;
}
.user-card:hover { border-color: var(--border-dark); }
.user-card .uc-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  background: linear-gradient(135deg, #0969da, #1a7f37);
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; font-weight: 700; color: #fff; flex-shrink: 0;
}
.user-card .uc-info { flex: 1; }
.user-card .uc-name { font-weight: 600; font-size: 15px; }
.user-card .uc-meta { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
.user-card .uc-actions { display: flex; gap: 6px; align-items: center; }

.perm-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 8px;
  margin-top: 12px;
}
.perm-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s;
}
.perm-item:hover { border-color: var(--primary); }
.perm-item input[type=checkbox] { accent-color: var(--primary); width: 15px; height: 15px; }
.perm-item.perm-on { border-color: #4ac26b; background: #dafbe1; color: var(--success); }
.perm-item.perm-off { border-color: #ffadb5; background: #ffebe9; color: var(--danger); }

.badge-admin { background: #ffd700; color: #7a4700; border: 1px solid #e6a000; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 700; }
.badge-user { background: #ddf4ff; color: #0969da; border: 1px solid #54aeff; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 700; }
.badge-inactive { background: #f0f0f0; color: #888; border: 1px solid #ddd; padding: 2px 8px; border-radius: 10px; font-size: 11px; }

/* DB switcher in sidebar */
.db-switcher {
  padding: 12px 16px;
  border-bottom: 1px solid var(--border);
  background: var(--surface2);
}
.db-switcher h3 { font-size:11px; text-transform:uppercase; letter-spacing:.5px; color:var(--text-tertiary); margin-bottom:8px; font-weight:600; }
.db-select-btn {
  width: 100%;
  padding: 7px 10px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  color: var(--text);
  font-family: inherit;
  font-size: 13px;
  font-weight: 500;
  text-align: left;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: border-color .15s;
}
.db-select-btn:hover { border-color: var(--primary); }
.db-select-btn .db-dot { width:8px; height:8px; border-radius:50%; background:var(--success); flex-shrink:0; }
.db-select-btn .db-name { flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.db-select-btn .db-arrow { color:var(--text-tertiary); font-size:10px; }
.db-list-dropdown {
  position: absolute;
  z-index: 500;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
  min-width: 220px;
  padding: 4px;
  display: none;
}
.db-list-dropdown.show { display: block; }
.db-list-dropdown .db-opt {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 10px; border-radius: 4px;
  cursor: pointer; font-size: 13px; font-weight: 500;
  transition: background .1s;
}
.db-list-dropdown .db-opt:hover { background: var(--surface2); }
.db-list-dropdown .db-opt.active { background: #ddf4ff; color: var(--primary); }
.db-list-dropdown .db-opt .db-dot { width:8px; height:8px; border-radius:50%; background:var(--border-dark); flex-shrink:0; }
.db-list-dropdown .db-opt.active .db-dot { background:var(--primary); }

/* DB management page */
.db-card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 16px 20px;
  display: flex; align-items: center; gap: 16px; margin-bottom: 10px;
}
.db-card .db-card-icon { font-size:28px; }
.db-card .db-card-info { flex:1; }
.db-card .db-card-name { font-weight:600; font-size:15px; }
.db-card .db-card-meta { font-size:12px; color:var(--text-secondary); margin-top:2px; }
.db-card .db-card-actions { display:flex; gap:6px; }

/* ─── Statistics ─── */
.stat-kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px; margin-bottom:20px; }
.kpi-card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 18px 20px;
  display: flex; flex-direction: column; gap: 6px; position: relative; overflow: hidden;
}
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
.kpi-card.kpi-blue::before { background:var(--primary); }
.kpi-card.kpi-green::before { background:var(--success); }
.kpi-card.kpi-orange::before { background:#d1680f; }
.kpi-card.kpi-purple::before { background:#6e40c9; }
.kpi-card.kpi-red::before { background:var(--danger); }
.kpi-card .kpi-icon { font-size: 22px; margin-bottom: 2px; }
.kpi-card .kpi-label { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: var(--text-tertiary); font-weight: 600; }
.kpi-card .kpi-value { font-size: 28px; font-weight: 700; color: var(--text); line-height: 1.1; }
.kpi-card .kpi-sub { font-size: 12px; color: var(--text-secondary); }
.kpi-card .kpi-delta { font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 3px; padding: 2px 7px; border-radius: 10px; width: fit-content; }
.kpi-delta.up { background: #e6ffed; color: var(--success); }
.kpi-delta.down { background: #ffebe9; color: var(--danger); }
.kpi-delta.neutral { background: var(--surface2); color: var(--text-tertiary); }

.stat-section { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 16px; overflow: hidden; }
.stat-section-head { display:flex; align-items:center; gap:8px; padding: 14px 18px; border-bottom: 1px solid var(--border); font-weight: 600; font-size: 14px; background: var(--surface2); }
.stat-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.stat-table th { padding: 10px 14px; text-align: left; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: var(--text-secondary); border-bottom: 2px solid var(--border); white-space: nowrap; background: var(--surface); }
.stat-table td { padding: 10px 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.stat-table tbody tr:last-child td { border-bottom: none; }
.stat-table tbody tr:hover td { background: var(--surface2); }
.stat-table .num { text-align: right; font-variant-numeric: tabular-nums; }
.stat-table .ctr { text-align: center; }

.delta-pos { color: var(--success); font-weight: 600; }
.delta-neg { color: var(--danger); font-weight: 600; }
.delta-neu { color: var(--text-tertiary); }

/* mini progress bar for days */
.stat-bar-wrap { display:flex; align-items:center; gap:8px; }
.stat-bar { flex:1; height:6px; background:var(--surface2); border-radius:3px; overflow:hidden; min-width:60px; }
.stat-bar-fill { height:100%; border-radius:3px; background:var(--primary); transition:width .3s; }
.stat-bar-text { font-size:12px; color:var(--text-secondary); white-space:nowrap; min-width:40px; text-align:right; }

/* absence dots */
.abs-dot { display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--danger); margin-right:2px; opacity:.7; }

/* ─── History ─── */
.hist-row { display: grid; grid-template-columns: 120px 120px 90px 80px 80px 1fr 90px 40px; gap: 8px; align-items: center; padding: 10px 16px; border-bottom: 1px solid var(--border); font-size: 13px; }
.hist-row:hover { background: var(--surface2); }
.hist-row.hist-header { background: var(--surface2); font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: var(--text-secondary); }
.hist-type-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.hist-type-full { background: #ddf4ff; color: var(--primary); }
.hist-type-brief { background: #e6ffed; color: var(--success); }
.hist-type-employee { background: #fff8c5; color: #7a4700; }
.hist-type-stats { background: #f3e8ff; color: #6e40c9; }

/* Modal */
.modal-overlay {
  position: fixed; inset: 0; z-index: 2000;
  background: rgba(0,0,0,0.5);
  display: flex; align-items: center; justify-content: center;
  backdrop-filter: blur(3px);
}
.modal-overlay.hidden { display: none; }
.modal-box {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 28px;
  width: 100%;
  max-width: 540px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.modal-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
.modal-footer { display: flex; gap: 8px; justify-content: flex-end; margin-top: 20px; }
.day-cell.drag-selected { outline: 2px solid var(--primary); outline-offset: -2px; background: rgba(37,99,235,0.12) !important; }
.ts-table-wrap.dragging { user-select: none; -webkit-user-select: none; }
.toast-container { position:fixed; top:20px; right:20px; z-index:10000; display:flex; flex-direction:column; gap:8px; }
.toast { padding:12px 20px; border-radius:var(--radius); color:#fff; font-size:14px; box-shadow:0 4px 12px rgba(0,0,0,.15); animation:slideIn .3s ease; max-width:400px; display:flex; align-items:center; gap:8px; }
.toast-success { background:#2e7d32; }
.toast-error { background:#c62828; }
.toast-info { background:#1565c0; }
.toast-warning { background:#e65100; }
@keyframes slideIn { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
.confirm-modal { position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center; }
.confirm-box { background:var(--surface);border-radius:12px;padding:24px;max-width:440px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.2); }
.confirm-title { font-size:16px;font-weight:700;margin-bottom:12px; }
.confirm-text { font-size:14px;line-height:1.6;color:var(--text-secondary);margin-bottom:20px;white-space:pre-line; }
.confirm-btns { display:flex;gap:8px;justify-content:flex-end; }
</style>

<div class="tabel-app-root">


<script>
const T = <?php echo json_encode($T); ?>;
const LANG = '<?php echo esc_js($lang); ?>';
const MONTH_KEYS = <?php echo json_encode($MK); ?>;
let currentYear = <?php echo (int)$current_year; ?>;
let currentMonth = <?php echo (int)$current_month; ?>;
var API_BASE = <?php echo json_encode($api_base); ?>;
var EXPORT_BASE = <?php echo json_encode($export_base); ?>;


let selectedYear = parseInt(localStorage.getItem('selectedYear')) || currentYear;
let selectedMonth = parseInt(localStorage.getItem('selectedMonth')) || currentMonth;

let currentPage = 'timesheet';
let employees = [];
let positions = [];
let timesheetData = null; // Данные текущего табеля
let editingEmpId = null;
let editingPosId = null;
</script>

<div class="app-container">
  
  <aside class="sidebar">
    <div class="sidebar-header">
      <h1><?php echo esc_html($T['app_title']); ?></h1>
    </div>

    <div class="sidebar-section">
      <h3><?php echo esc_html($T['language']); ?></h3>
      <div class="lang-row">
        <button class="lang-btn <?php if ($lang == 'ru') echo 'active'; ?>" onclick="setLang('ru')">RU</button>
        <button class="lang-btn <?php if ($lang == 'kg') echo 'active'; ?>" onclick="setLang('kg')">KG</button>
        <button class="lang-btn <?php if ($lang == 'en') echo 'active'; ?>" onclick="setLang('en')">EN</button>
      </div>
    </div>

    <div class="sidebar-section">
      <h3><?php echo esc_html($T['select_month']); ?></h3>
      <div class="month-selector">
        <select id="monthSelect" onchange="changeMonth()">
          <?php foreach ($MK as $i => $mk): $idx = $i + 1; ?>
          <option value="<?php echo $idx; ?>" <?php if ($idx == $current_month) echo 'selected'; ?>><?php echo esc_html($T[$mk]); ?></option>
          <?php endforeach; ?>
        </select>
        <input type="number" id="yearInput" value="<?php echo (int)$current_year; ?>" min="2020" max="2040" style="width:80px" onchange="changeMonth()">
      </div>
      <div style="display:flex; gap:4px; margin-top:6px;">
        <button class="quick-btn" onclick="goPrevMonth()"><?php echo esc_html($T['prev_month']); ?></button>
        <button class="quick-btn" onclick="goCurrentMonth()"><?php echo esc_html($T['current_month']); ?></button>
        <button class="quick-btn" onclick="goNextMonth()"><?php echo esc_html($T['next_month']); ?></button>
      </div>
    </div>

    <!-- DB Switcher -->
    <div class="db-switcher" id="dbSwitcherWrap" style="position:relative;">
      <h3>База данных</h3>
      <button class="db-select-btn" onclick="toggleDbDropdown()" id="dbSelectBtn">
        <span class="db-dot"></span>
        <span class="db-name" id="dbActiveName">Не выбрана</span>
        <span id="dbTypeBadge" style="font-size:10px;font-weight:600;padding:1px 6px;border-radius:8px;background:#ddf4ff;color:var(--primary)">🎓 ППС</span>
        <span class="db-arrow">▼</span>
      </button>
      <div class="db-list-dropdown" id="dbDropdown"></div>
    </div>

    <div class="sidebar-section" style="flex:1;">
      <h3>Меню</h3>
      <button class="nav-btn active" id="nav-timesheet" onclick="showPage('timesheet')"
              data-tooltip="<?php echo esc_html($T['hint_day_cell']); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <?php echo esc_html($T['timesheet']); ?>
      </button>
      <button class="nav-btn" id="nav-employees" onclick="showPage('employees')"
              data-tooltip="<?php echo esc_html($T['hint_fio']); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        <?php echo esc_html($T['employees']); ?>
      </button>
      <button class="nav-btn" id="nav-positions" onclick="showPage('positions')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
        <?php echo esc_html($T['positions']); ?>
      </button>
      <button class="nav-btn" id="nav-excel-settings" onclick="showPage('excel-settings')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        Excel Баштык / Белгилер
      </button>
      <button class="nav-btn" id="nav-experience" onclick="showPage('experience')" style="display:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Стаж
      </button>
      <button class="nav-btn" id="nav-users" onclick="showPage('users')" style="display:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/><path d="M16 11l2 2 4-4"/></svg>
        Пользователи
      </button>
      <button class="nav-btn" id="nav-databases" onclick="showPage('databases')" style="display:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
        Базы данных
      </button>
      <button class="nav-btn" id="nav-statistics" onclick="showPage('statistics')" style="display:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        <?php echo esc_html($T['statistics']); ?>
      </button>
      <button class="nav-btn" id="nav-history" onclick="showPage('history')" style="display:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <?php echo esc_html($T['export_history']); ?>
      </button>
      <button class="nav-btn" id="nav-notifications" onclick="showPage('notifications')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        Уведомления <span id="notifBadge" style="display:none;background:var(--danger);color:#fff;font-size:10px;padding:1px 5px;border-radius:9px;margin-left:2px"></span>
      </button>
      <button class="nav-btn" id="nav-workflow" onclick="showPage('workflow')" style="display:none">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M4 20L21 3"/><path d="M21 16v5h-5"/><path d="M15 15l6 6"/><path d="M4 4l5 5"/></svg>
        Цепочки
      </button>
    </div>

    <!-- User bar at bottom -->
    <div class="user-bar">
      <div class="user-avatar" id="sidebarAvatar">?</div>
      <div style="flex:1; min-width:0;">
        <div class="user-name" id="sidebarUsername">...</div>
        <div class="user-role" id="sidebarRole">загрузка...</div>
      </div>
      <button class="btn-logout" onclick="fetch(API_BASE + 'logout').then(() => window.location.reload())">Выйти</button>
    </div>
  </aside>


  <main class="main-content">

    <div class="page" id="page-employees">
      <div class="page-title"><?php echo esc_html($T['employees']); ?></div>

      <div class="emp-form" id="empForm">
        <div class="form-group" style="position:relative;">
          <label><?php echo esc_html($T['full_name']); ?></label>
          <input type="text" id="empName" placeholder="<?php echo esc_html($T['full_name']); ?>" autocomplete="off" oninput="searchEmployees(this.value)">
          <div class="autocomplete-list" id="nameAutocomplete"></div>
        </div>
        <div class="form-group">
          <label><?php echo esc_html($T['position']); ?></label>
          <select id="empPosition" onchange="updateRateCalculation()">
            <option value=""><?php echo esc_html($T['position']); ?></option>
          </select>
        </div>
        <div class="form-group">
          <label><?php echo esc_html($T['employment_conditions']); ?></label>
          <select id="empEmployment">
            <option value="">-</option>
            <option value="staff_internal"><?php echo esc_html($T['staff_internal']); ?></option>
            <option value="part_time_internal"><?php echo esc_html($T['part_time']); ?> <?php echo esc_html($T['internal']); ?></option>
            <option value="part_time_external"><?php echo esc_html($T['part_time']); ?> <?php echo esc_html($T['external']); ?></option>
          </select>
        </div>
        <div class="form-group pps-only-field">
          <label><?php echo esc_html($T['pedagog_experience']); ?></label>
          <input type="text" id="empExperience" placeholder="<?php echo esc_html($T['pedagog_experience']); ?>">
        </div>
        <div class="form-group pps-only-field">
          <label><?php echo esc_html($T['actual_hours']); ?></label>
          <input type="number" id="empActualHours" step="0.01" placeholder="0" oninput="updateRateCalculation()">
        </div>
        <div class="form-group">
          <label><?php echo esc_html($T['pay_type']); ?></label>
          <select id="empPayType" onchange="toggleRateLabel()">
            <option value="rate"><?php echo esc_html($T['rate_based']); ?></option>
            <option value="fixed"><?php echo esc_html($T['fixed_based']); ?></option>
          </select>
        </div>
        <div class="form-group">
          <label id="rateLabel"><?php echo esc_html($T['rate']); ?></label>
          <input type="number" id="empRate" step="0.01" placeholder="0.00">
        </div>
        <div style="display:flex; gap:6px; align-items:end;">
          <button class="btn btn-primary" onclick="saveEmployee()"><?php echo esc_html($T['save']); ?></button>
          <button class="btn btn-ghost" id="cancelEditBtn" style="display:none" onclick="cancelEdit()"><?php echo esc_html($T['cancel']); ?></button>
        </div>
      </div>

      <div class="emp-grid" id="empGrid"></div>
    </div>

    <!-- Страница должностей -->
    <div class="page" id="page-positions">
      <div class="page-title"><?php echo esc_html($T['positions']); ?></div>

      <div class="emp-form" id="posForm">
        <div class="form-group">
          <label>Название (RU)</label>
          <input type="text" id="posNameRu" placeholder="Название (RU)">
        </div>
        <div class="form-group">
          <label>Аталышы (KG)</label>
          <input type="text" id="posNameKg" placeholder="Аталышы (KG)">
        </div>
        <div class="form-group">
          <label>Name (EN)</label>
          <input type="text" id="posNameEn" placeholder="Name (EN)">
        </div>
        <div class="form-group pps-only-field">
          <label><?php echo esc_html($T['planned_hours']); ?></label>
          <input type="number" id="posHours" placeholder="850" min="0">
        </div>
        <div style="display:flex; gap:6px; align-items:end;">
          <button class="btn btn-primary" onclick="savePosition()"><?php echo esc_html($T['save']); ?></button>
          <button class="btn btn-ghost" id="cancelPosBtn" style="display:none" onclick="cancelEditPos()"><?php echo esc_html($T['cancel']); ?></button>
        </div>
      </div>

      <div class="emp-grid" id="posGrid"></div>
    </div>

   
    <div class="page active" id="page-timesheet">
      <div class="page-title">
        <?php echo esc_html($T['timesheet']); ?>
        <span id="tsMonthLabel" style="color:var(--text-secondary); font-size:16px; font-weight:500;"></span>

        <div style="margin-left:auto; display:flex; gap:6px; align-items:center;">
          <div class="export-menu">
            <button class="btn btn-success" onclick="toggleExportMenu()" data-tooltip="<?php echo esc_html($T['hint_export']); ?>">
              <?php echo esc_html($T['export_excel']); ?>
            </button>
            <div class="export-dropdown" id="exportDropdown">
              <a href="#" onclick="exportFull(); return false;"><?php echo esc_html($T['export_full']); ?></a>
              <a href="#" onclick="exportBrief(); return false;"><?php echo esc_html($T['export_brief']); ?></a>
              <div id="exportByEmployee"></div>
            </div>
          </div>
        </div>
      </div>

      <div id="tsContent"></div>
    </div>


    <!-- Страница настроек Excel -->
    <div class="page" id="page-excel-settings">
      <div class="page-title">
        <?php echo esc_html($T['es_title']); ?>
        <button class="btn btn-primary" style="margin-left:auto" onclick="saveExcelSettings()" id="excelSaveBtn">💾 <?php echo esc_html($T['es_save_all']); ?></button>
      </div>

      <!-- DOCUMENT TITLE -->
      <div class="es-panel" style="margin-bottom:20px">
        <div class="es-panel-title">📝 Заголовок документа</div>
        <div class="form-group">
          <label>Название табеля в Excel</label>
          <input type="text" id="s_doc_title" placeholder="Профессордук-окутуучулук курамдын жумуш убактысын эсепке алуу" style="width:100%">
        </div>
      </div>

      <!-- HEADERS SECTION -->
      <div class="es-panel" style="margin-bottom:20px">
        <div class="es-panel-title">📄 <?php echo esc_html($T['es_header_panel']); ?></div>

        <div class="es-cols-header">
          <span><?php echo esc_html($T['es_left']); ?></span>
          <span><?php echo esc_html($T['es_center']); ?></span>
          <span><?php echo esc_html($T['es_right']); ?></span>
        </div>

        <div class="es-row3">
          <div class="form-group"><label><?php echo esc_html($T['es_left_col']); ?> <?php echo esc_html($T['es_row1']); ?></label><input type="text" id="s_header_left1" placeholder="Макулдашылды"></div>
          <div class="form-group"><label><?php echo esc_html($T['es_center_col']); ?> <?php echo esc_html($T['es_row1']); ?></label><input type="text" id="s_header_center1" placeholder="Университет аты"></div>
          <div class="form-group"><label><?php echo esc_html($T['es_right_col']); ?> <?php echo esc_html($T['es_row1']); ?></label><input type="text" id="s_header_right1" placeholder="БЕКИТЕМ"></div>
          <div class="form-group"><label><?php echo esc_html($T['es_left_col']); ?> <?php echo esc_html($T['es_row2']); ?></label><input type="text" id="s_header_left2" placeholder="Кызмат"></div>
          <div class="form-group"><label><?php echo esc_html($T['es_center_col']); ?> <?php echo esc_html($T['es_row2']); ?></label><input type="text" id="s_header_center2" placeholder="Факультет"></div>
          <div class="form-group"><label><?php echo esc_html($T['es_right_col']); ?> <?php echo esc_html($T['es_row2']); ?></label><input type="text" id="s_header_right2" placeholder="Ректор кызматы"></div>
          <div class="form-group"><label><?php echo esc_html($T['es_left_col']); ?> <?php echo esc_html($T['es_row3']); ?></label><input type="text" id="s_header_left3" placeholder="____ Аты-жөнү"></div>
          <div class="form-group"><label><?php echo esc_html($T['es_center_col']); ?> <?php echo esc_html($T['es_row3']); ?> (<?php echo esc_html($T['es_department']); ?>)</label><input type="text" id="s_header_center3" placeholder="Кафедра аты"></div>
          <div class="form-group"><label><?php echo esc_html($T['es_right_col']); ?> <?php echo esc_html($T['es_row3']); ?></label><input type="text" id="s_header_right3" placeholder="____ Аты-жөнү"></div>
        </div>

        <div class="es-section-title">✍️ <?php echo esc_html($T['es_signatures']); ?></div>
        <div class="es-sigs">
          <div class="form-group"><label><?php echo esc_html($T['es_sig1']); ?></label><input type="text" id="s_sig1" placeholder="Кафедра башчысы: ..."></div>
          <div class="form-group"><label><?php echo esc_html($T['es_sig2']); ?></label><input type="text" id="s_sig2" placeholder="ОМБнун башчысы: ..."></div>
          <div class="form-group"><label><?php echo esc_html($T['es_sig3']); ?></label><input type="text" id="s_sig3" placeholder="Укук иштери бөлүмү: ..."></div>
        </div>
      </div>

      <!-- MARKS SECTION -->
      <div class="es-panel">
        <div class="es-panel-title">🏷️ <?php echo esc_html($T['es_marks_panel']); ?></div>

        <div style="display:grid;grid-template-columns:36px 70px 1fr 36px;gap:8px;padding:6px 0;border-bottom:2px solid var(--border);margin-bottom:4px">
          <span style="font-size:11px;font-weight:600;color:var(--text-tertiary);text-transform:uppercase">🎨</span>
          <span style="font-size:11px;font-weight:600;color:var(--text-tertiary);text-transform:uppercase"><?php echo esc_html($T['es_symbol']); ?></span>
          <span style="font-size:11px;font-weight:600;color:var(--text-tertiary);text-transform:uppercase"><?php echo esc_html($T['es_description']); ?></span>
          <span></span>
        </div>

        <div id="marksSettingsList"></div>
        <button class="btn btn-ghost" onclick="addMark()" style="margin-top:8px;font-size:13px">＋ <?php echo esc_html($T['es_add_mark']); ?></button>
      </div>
    </div>

    <!-- Стаж страница -->
    <div class="page" id="page-experience">
      <div class="page-title">Стаж сотрудников</div>
      <div style="margin-bottom:12px">
        <input type="text" id="expSearch" placeholder="Поиск сотрудника..." oninput="filterExpEmployees(this.value)" style="width:100%;max-width:400px;padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius)">
      </div>
      <div id="expEmployeesList"></div>
      
      <!-- Experience modal -->
      <div class="modal-overlay" id="expModal" style="display:none" onclick="if(event.target===this)closeExpModal()">
        <div class="modal-box" style="max-width:600px">
          <div class="modal-title" id="expModalTitle">Стаж</div>
          <div id="expPeriodsContainer"></div>
          <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
            <div style="font-weight:600;font-size:14px;margin-bottom:8px">Добавить период</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div class="form-group"><label>Дата начала</label><input type="date" id="expDateFrom"></div>
              <div class="form-group"><label>Дата окончания</label><input type="date" id="expDateTo"></div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;margin:8px 0">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                <input type="checkbox" id="expIsCurrent" onchange="document.getElementById('expDateTo').disabled=this.checked"> Работает по сей день
              </label>
            </div>
            <div class="form-group"><label>Примечание</label><input type="text" id="expNote" placeholder="Место работы, должность..."></div>
            <button class="btn btn-primary" onclick="saveExpPeriod()">Добавить период</button>
          </div>
          <div style="margin-top:12px;padding:12px;background:var(--surface2);border-radius:var(--radius)">
            <strong>Итого стаж: <span id="expTotalDisplay">—</span></strong>
          </div>
          <div style="margin-top:12px;text-align:right">
            <button class="btn btn-ghost" onclick="closeExpModal()">Закрыть</button>
          </div>
        </div>
      </div>
    </div>

    <!-- База данных страница -->
    <div class="page" id="page-databases">
      <div class="page-title">
        🗄️ Базы данных
        <button class="btn btn-primary" style="margin-left:auto;display:none" id="createDbBtn" onclick="openCreateDbModal()">
          ＋ Создать БД
        </button>
      </div>
      <div id="dbsGrid"></div>
    </div>

    <!-- STATISTICS PAGE -->
    <div class="page" id="page-statistics">
      <div class="page-title" style="flex-wrap:wrap;gap:10px">
        📊 <?php echo esc_html($T['statistics']); ?>
        <div style="display:flex;gap:8px;margin-left:auto;align-items:center;flex-wrap:wrap">
          <select id="statYear" onchange="loadStats()" style="padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius);font-family:inherit;font-size:13px"></select>
          <select id="statMonth" onchange="loadStats()" style="padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius);font-family:inherit;font-size:13px"></select>
          <button class="btn btn-ghost" onclick="exportStats()" style="font-size:13px">📥 Экспорт Excel</button>
        </div>
      </div>
      <div id="statContent"><div style="padding:32px;text-align:center;color:var(--text-tertiary)">Выберите месяц для отображения статистики</div></div>
    </div>

    <!-- HISTORY PAGE -->
    <div class="page" id="page-history">
      <div class="page-title" style="flex-wrap:wrap;gap:10px">
        🕐 <?php echo esc_html($T['export_history']); ?>
        <div style="display:flex;gap:8px;margin-left:auto;align-items:center;flex-wrap:wrap" id="historyFilters">
          <select id="histDbFilter" onchange="loadHistory(1)" style="padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius);font-family:inherit;font-size:13px;display:none">
            <option value="">Все базы</option>
          </select>
          <button class="btn btn-ghost" onclick="clearExportHistory()" style="font-size:12px;color:var(--danger)" id="btnClearHistory">🗑 Очистить всю историю</button>
        </div>
      </div>
      <div id="historyTable"></div>
      <div id="historyPager" style="display:flex;gap:8px;justify-content:center;margin-top:16px"></div>
    </div>

    <!-- Страница управления пользователями (только суперадмин) -->
    <div class="page" id="page-users">
      <div class="page-title">
        👥 Управление пользователями
        <button class="btn btn-primary" style="margin-left:auto" onclick="openCreateUserModal()">＋ Создать аккаунт</button>
      </div>
      <div id="usersGrid"></div>
      
      <div style="margin-top:32px;border-top:2px solid var(--border);padding-top:20px">
        <div class="page-title" style="margin-bottom:12px">
          🔐 Неудачные попытки входа
          <div style="margin-left:auto;display:flex;gap:6px">
            <button class="btn btn-ghost" style="font-size:12px" id="logFilterAll" onclick="loadLoginLogs('all')">Все</button>
            <button class="btn btn-ghost" style="font-size:12px" id="logFilterOk" onclick="loadLoginLogs('success')">✅ Успешные</button>
            <button class="btn btn-ghost" style="font-size:12px;color:var(--danger)" id="logFilterFail" onclick="loadLoginLogs('failed')">❌ Неудачные</button>
          </div>
        </div>
        <div id="loginLogsContent" style="font-size:13px"></div>
      </div>
    </div>

    <!-- Notifications page -->
    <div class="page" id="page-notifications">
      <div class="page-title">🔔 Уведомления <button class="btn btn-ghost" style="margin-left:auto;font-size:12px" onclick="markNotificationsRead()">Прочитать все</button></div>
      <div id="notificationsContent"></div>
    </div>

    <!-- Workflow page (superadmin only) -->
    <div class="page" id="page-workflow">
      <div class="page-title">🔗 Цепочки согласования</div>
      <div id="workflowContent"></div>
    </div>

  </main>
</div>

<!-- Modal: create database -->
<div class="modal-overlay hidden" id="dbModal">
  <div class="modal-box" style="max-width:440px">
    <div class="modal-title">🗄️ Новая база данных</div>
    <div class="form-group">
      <label>Отображаемое название</label>
      <input type="text" id="db_display_name" placeholder="Кафедра МД">
    </div>
    <div class="form-group">
      <label>Системное имя (латиница, без пробелов)</label>
      <input type="text" id="db_sys_name" placeholder="kafedra_md">
    </div>

    <!-- Тип БД -->
    <div style="margin-bottom:16px">
      <label style="font-size:12px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:10px">Тип базы данных</label>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        <label id="dbTypeCardPps" onclick="selectDbType('pps')" style="cursor:pointer;border:2px solid var(--primary);border-radius:8px;padding:14px;background:#ddf4ff">
          <input type="radio" name="db_type" value="pps" checked style="display:none">
          <div style="font-size:20px;margin-bottom:6px">🎓</div>
          <div style="font-weight:700;font-size:14px;margin-bottom:4px">ППС</div>
          <div style="font-size:12px;color:var(--text-secondary)">Для преподавателей. Плановые/факт. часы, пед. стаж, ставка × 6 в день.</div>
        </label>
        <label id="dbTypeCardStaff" onclick="selectDbType('staff')" style="cursor:pointer;border:2px solid var(--border);border-radius:8px;padding:14px;background:var(--surface)">
          <input type="radio" name="db_type" value="staff" style="display:none">
          <div style="font-size:20px;margin-bottom:6px">👷</div>
          <div style="font-weight:700;font-size:14px;margin-bottom:4px">Сотрудники</div>
          <div style="font-size:12px;color:var(--text-secondary)">Пн–Пт ставка × 7, Сб ставка × 5. Без плановых часов и пед. стажа.</div>
        </label>
      </div>
    </div>

    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeDbModal()">Отмена</button>
      <button class="btn btn-primary" onclick="saveDbModal()">Создать</button>
    </div>
  </div>
</div>

<!-- Modal: assign DB to users -->
<div class="modal-overlay hidden" id="dbAssignModal">
  <div class="modal-box" style="max-width:480px">
    <div class="modal-title" id="dbAssignTitle">Назначить пользователей</div>
    <div id="dbAssignUserList"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeDbAssignModal()">Отмена</button>
      <button class="btn btn-primary" onclick="saveDbAssign()">Сохранить</button>
    </div>
  </div>
</div>

<!-- Modal: create/edit user -->
<div class="modal-overlay hidden" id="userModal">
  <div class="modal-box">
    <div class="modal-title" id="userModalTitle">Создать аккаунт</div>
    
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
      <div class="form-group">
        <label>Логин</label>
        <input type="text" id="mu_username" placeholder="username">
      </div>
      <div class="form-group">
        <label>Пароль <span style="font-weight:400;color:var(--text-tertiary)" id="pwHint">(обязательно)</span></label>
        <input type="password" id="mu_password" placeholder="••••••••">
      </div>
    </div>

    <div style="font-weight:600; font-size:13px; margin-bottom:10px; color:var(--text);">🔐 Права доступа</div>
    <div class="perm-grid" id="permGrid"></div>

    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeUserModal()">Отмена</button>
      <button class="btn btn-primary" onclick="saveUserModal()" id="mu_save_btn">Сохранить</button>
    </div>
  </div>
</div>


<div class="cell-dropdown" id="cellDropdown">
  <button onclick="setCellStatus('work')"><span class="dot dot-work"></span><?php echo esc_html($T['work']); ?></button>
  <button onclick="setCellStatus('day_off')"><span class="dot dot-day_off"></span><?php echo esc_html($T['day_off']); ?></button>
  <div style="border-top:1px solid var(--border); margin:4px 0; padding-top:4px; font-size:10px; color:var(--text-tertiary); padding-left:12px; font-weight:600;"><?php echo esc_html($T['official_marks']); ?></div>
  <button onclick="setCellStatus('sick')"><span class="dot dot-sick"></span><?php echo esc_html($T['sick']); ?> (О)</button>
  <button onclick="setCellStatus('vacation')"><span class="dot" style="background:#9C27B0"></span><?php echo esc_html($T['vacation']); ?> (К)</button>
  <button onclick="setCellStatus('business_trip')"><span class="dot" style="background:#FF9800"></span><?php echo esc_html($T['business_trip']); ?> (С)</button>
  <button onclick="setCellStatus('maternity')"><span class="dot" style="background:#E91E63"></span><?php echo esc_html($T['maternity']); ?> (КӨ)</button>
  <button onclick="setCellStatus('childcare')"><span class="dot" style="background:#FF5722"></span><?php echo esc_html($T['childcare']); ?> (БӨ)</button>
  <button onclick="setCellStatus('study_leave')"><span class="dot" style="background:#607D8B"></span><?php echo esc_html($T['study_leave']); ?> (ОӨ)</button>
  <button onclick="setCellStatus('admin_leave')"><span class="dot" style="background:#795548"></span><?php echo esc_html($T['admin_leave']); ?> (АӨ)</button>
  <button onclick="setCellStatus('absence')"><span class="dot" style="background:#F44336"></span><?php echo esc_html($T['absence']); ?> (Дк)</button>
  <button onclick="setCellStatus('late')"><span class="dot" style="background:#FF6F00"></span><?php echo esc_html($T['late']); ?> (Кк)</button>
  <button onclick="setCellStatus('early_leave')"><span class="dot" style="background:#BF360C"></span><?php echo esc_html($T['early_leave']); ?> (Эк)</button>
  <div class="custom-input-row">
    <input type="text" id="customCellValue" placeholder="<?php echo esc_html($T['enter_value']); ?>">
    <button onclick="setCellCustom()">OK</button>
  </div>
</div>

<div class="cell-dropdown" id="dayDropdown">
  <div style="padding: 6px 12px; font-size: 11px; color: var(--text-tertiary); font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--border); margin-bottom: 4px;"><?php echo esc_html($T['for_all_employees']); ?></div>
  <button onclick="setDayStatusForAll('work')"><span class="dot dot-work"></span><?php echo esc_html($T['work']); ?></button>
  <button onclick="setDayStatusForAll('day_off')"><span class="dot dot-day_off"></span><?php echo esc_html($T['day_off']); ?></button>
  <div style="border-top:1px solid var(--border); margin:4px 0; padding-top:4px; font-size:10px; color:var(--text-tertiary); padding-left:12px; font-weight:600;"><?php echo esc_html($T['official_marks']); ?></div>
  <button onclick="setDayStatusForAll('sick')"><span class="dot dot-sick"></span><?php echo esc_html($T['sick']); ?> (О)</button>
  <button onclick="setDayStatusForAll('vacation')"><span class="dot" style="background:#9C27B0"></span><?php echo esc_html($T['vacation']); ?> (К)</button>
  <button onclick="setDayStatusForAll('business_trip')"><span class="dot" style="background:#FF9800"></span><?php echo esc_html($T['business_trip']); ?> (С)</button>
  <button onclick="setDayStatusForAll('maternity')"><span class="dot" style="background:#E91E63"></span><?php echo esc_html($T['maternity']); ?> (КӨ)</button>
  <button onclick="setDayStatusForAll('childcare')"><span class="dot" style="background:#FF5722"></span><?php echo esc_html($T['childcare']); ?> (БӨ)</button>
  <button onclick="setDayStatusForAll('study_leave')"><span class="dot" style="background:#607D8B"></span><?php echo esc_html($T['study_leave']); ?> (ОӨ)</button>
  <button onclick="setDayStatusForAll('admin_leave')"><span class="dot" style="background:#795548"></span><?php echo esc_html($T['admin_leave']); ?> (АӨ)</button>
  <button onclick="setDayStatusForAll('absence')"><span class="dot" style="background:#F44336"></span><?php echo esc_html($T['absence']); ?> (Дк)</button>
  <button onclick="setDayStatusForAll('late')"><span class="dot" style="background:#FF6F00"></span><?php echo esc_html($T['late']); ?> (Кк)</button>
  <button onclick="setDayStatusForAll('early_leave')"><span class="dot" style="background:#BF360C"></span><?php echo esc_html($T['early_leave']); ?> (Эк)</button>
</div>

<div class="cell-dropdown" id="positionDropdown">
  <div style="padding: 6px 12px; font-size: 11px; color: var(--text-tertiary); font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--border); margin-bottom: 4px;"><?php echo esc_html($T['select_position']); ?></div>
  <div id="positionDropdownContent"></div>
</div>

<div class="cell-dropdown" id="employmentDropdown">
  <div style="padding: 6px 12px; font-size: 11px; color: var(--text-tertiary); font-weight: 600; text-transform: uppercase; border-bottom: 1px solid var(--border); margin-bottom: 4px;">Тип занятости</div>
  <div id="employmentDropdownContent"></div>
</div>

<script>

let dropdownTarget = null;
let mePerms = {};
let meIsAdmin = false;
let meUserId = null;


function showPage(page) {
  currentPage = page;
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById('page-' + page).classList.add('active');
  document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('nav-' + page).classList.add('active');
  if (page === 'employees') loadEmployees();
  if (page === 'positions') loadPositions();
  if (page === 'timesheet') loadTimesheet();
  if (page === 'excel-settings') loadExcelSettings();
  if (page === 'experience') loadExperiencePage();
  if (page === 'users') loadUsers();
  if (page === 'databases') loadDatabases();
  if (page === 'statistics') loadStats();
  if (page === 'history') loadHistory(1);
  if (page === 'notifications') loadNotifications();
  if (page === 'workflow') loadWorkflowPage();
}

function setLang(lang) {
  fetch(API_BASE + 'set_lang/' + lang).then(() => location.reload());
}

function changeMonth() {
  selectedMonth = parseInt(document.getElementById('monthSelect').value);
  selectedYear = parseInt(document.getElementById('yearInput').value);
  localStorage.setItem('selectedMonth', selectedMonth);
  localStorage.setItem('selectedYear', selectedYear);
  loadTimesheet();
}

function goCurrentMonth() {
  selectedYear = currentYear;
  selectedMonth = currentMonth;
  localStorage.setItem('selectedMonth', selectedMonth);
  localStorage.setItem('selectedYear', selectedYear);
  document.getElementById('monthSelect').value = selectedMonth;
  document.getElementById('yearInput').value = selectedYear;
  loadTimesheet();
}

function goPrevMonth() {
  let m = selectedMonth - 1, y = selectedYear;
  if (m < 1) { m = 12; y--; }
  selectedYear = y;
  selectedMonth = m;
  localStorage.setItem('selectedMonth', selectedMonth);
  localStorage.setItem('selectedYear', selectedYear);
  document.getElementById('monthSelect').value = selectedMonth;
  document.getElementById('yearInput').value = selectedYear;
  loadTimesheet();
}

function goNextMonth() {
  let m = selectedMonth + 1, y = selectedYear;
  if (m > 12) { m = 1; y++; }
  selectedYear = y;
  selectedMonth = m;
  localStorage.setItem('selectedMonth', selectedMonth);
  localStorage.setItem('selectedYear', selectedYear);
  document.getElementById('monthSelect').value = selectedMonth;
  document.getElementById('yearInput').value = selectedYear;
  loadTimesheet();
}


async function loadEmployees() {
  try {
    const res = await fetch(API_BASE + 'employees');
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    employees = await res.json();
    renderEmployees();
  } catch (error) {
    console.error('Failed to load employees:', error);
  }
}

function renderEmployees() {
  const grid = document.getElementById('empGrid');
  if (!employees.length) {
    grid.innerHTML = `<div class="empty-state"><p>${T.no_employees}</p></div>`;
    return;
  }
  grid.innerHTML = employees.map(e => {
    const posName = e.position_name || e.position || '';
    const empType = e.employment_internal || e.employment_external || '';
    const empTypeLabel = empType === 'staff' ? (T.staff || 'Штат') : 
                        empType === 'part_time' ? (T.part_time || 'Совм.') : '';
    
    return `
    <div class="emp-card">
      <div class="emp-info">
        <div class="emp-name">${e.full_name}</div>
        <div class="emp-pos">${posName}</div>
        <div class="emp-meta">
          ${empTypeLabel ? `<span class="emp-badge badge-rate">${empTypeLabel}</span>` : ''}
          <span class="emp-badge badge-rate">${T.rate || 'Ставка'}: ${e.rate || 0}</span>
          ${e.pedagog_experience ? `<span class="emp-badge badge-fixed">${e.pedagog_experience}</span>` : ''}
        </div>
      </div>
      <div class="emp-actions">
        <button class="btn btn-ghost" onclick="editEmployee(${e.id})"><?php echo esc_html($T['edit']); ?></button>
        <button class="btn btn-danger" onclick="deleteEmployee(${e.id})"><?php echo esc_html($T['delete']); ?></button>
      </div>
    </div>
  `;
  }).join('');
}

async function saveEmployee() {
  if (!editingEmpId && isWorkflowBlocked()) { showToast('🔒 Нельзя добавлять сотрудников — не ваша очередь в цепочке','warning'); return; }
  const posId = document.getElementById('empPosition').value;
  const posName = positions.find(p => p.id == posId)?.name || '';
  
  const empValue = document.getElementById('empEmployment').value;
  let empInternal = null;
  let empExternal = null;
  if (empValue === 'staff_internal') empInternal = 'staff';
  else if (empValue === 'part_time_internal') empInternal = 'part_time';
  else if (empValue === 'part_time_external') empExternal = 'part_time';
  
  const data = {
    full_name: document.getElementById('empName').value.trim(),
    position: posName,
    position_id: posId ? parseInt(posId) : null,
    pay_type: document.getElementById('empPayType').value,
    rate: parseFloat(document.getElementById('empRate').value) || 0,
    employment_internal: empInternal,
    employment_external: empExternal,
    pedagog_experience: document.getElementById('empExperience').value.trim() || null,
    actual_hours: parseFloat(document.getElementById('empActualHours').value) || null,
  };
  if (!data.full_name) return;


  try {
    if (editingEmpId) {
      const response = await fetch(API_BASE + `employees/${editingEmpId}`, { 
        method:'PUT', 
        headers:{'Content-Type':'application/json'}, 
        body: JSON.stringify(data) 
      });
      const result = await response.json();
      editingEmpId = null;
      document.getElementById('cancelEditBtn').style.display = 'none';
    } else {
      const response = await fetch(API_BASE + 'employees', { 
        method:'POST', 
        headers:{'Content-Type':'application/json'}, 
        body: JSON.stringify(data) 
      });
      const result = await response.json();
    }
    
    // Очищаем форму и принудительно перезагружаем
    clearForm();
    
    // Принудительная перезагрузка с небольшой задержкой
    setTimeout(async () => {
      await loadEmployees();
      await loadTimesheet();
    }, 100);
    
  } catch (error) {
    console.error('ERROR: Failed to save employee:', error);
    showToast('Ошибка при сохранении','error');
  }
}

function editEmployee(id) {
  const e = employees.find(x => x.id == id);
  if (!e) return;
  editingEmpId = id;
  document.getElementById('empName').value = e.full_name;
  document.getElementById('empPosition').value = e.position_id || '';
  document.getElementById('empPayType').value = e.pay_type;
  document.getElementById('empRate').value = e.rate;
  // Устанавливаем единый select условий работы
  let empVal = '';
  if (e.employment_internal === 'staff') empVal = 'staff_internal';
  else if (e.employment_internal === 'part_time') empVal = 'part_time_internal';
  else if (e.employment_external === 'part_time') empVal = 'part_time_external';
  document.getElementById('empEmployment').value = empVal;
  document.getElementById('empExperience').value = e.pedagog_experience || '';
  document.getElementById('empActualHours').value = e.actual_hours || '';
  document.getElementById('cancelEditBtn').style.display = 'inline-flex';
  toggleRateLabel();
}

async function deleteEmployee(id) {
  if (!confirm(T.confirm_delete)) return;
  await fetch(API_BASE + `employees/${id}`, { method:'DELETE' });
  await loadEmployees();
  await loadTimesheet();
}

function cancelEdit() {
  editingEmpId = null;
  document.getElementById('cancelEditBtn').style.display = 'none';
  clearForm();
}

function clearForm() {
  document.getElementById('empName').value = '';
  document.getElementById('empPosition').value = '';
  document.getElementById('empPayType').value = 'rate';
  document.getElementById('empRate').value = '';
  document.getElementById('empEmployment').value = '';
  document.getElementById('empExperience').value = '';
  document.getElementById('empActualHours').value = '';
  toggleRateLabel();
}

function toggleRateLabel() {
  const pt = document.getElementById('empPayType').value;
  const rateInput = document.getElementById('empRate');

  if (pt === 'fixed') {
    // Фиксированная сумма — поле редактируемое
    document.getElementById('rateLabel').innerHTML = T.fixed || 'Фиксированная сумма';
    rateInput.readOnly = false;
    rateInput.style.background = 'var(--surface)';
    rateInput.style.cursor = 'text';
    rateInput.placeholder = 'Введите сумму';
  } else {
    // По ставке:
    // - Для ППС: ставка считается автоматически из факт.часов → readOnly
    // - Для Сотрудников: ставка вводится вручную → редактируемая
    document.getElementById('rateLabel').innerHTML = T.rate || 'Ставка';
    if (activeDbType === 'staff') {
      rateInput.readOnly = false;
      rateInput.style.background = 'var(--surface)';
      rateInput.style.cursor = 'text';
      rateInput.placeholder = '0.00';
    } else {
      rateInput.readOnly = true;
      rateInput.style.background = 'var(--surface2)';
      rateInput.style.cursor = 'not-allowed';
      rateInput.placeholder = '0.00 (рассчитывается)';
    }
  }
}

function updateRateCalculation() {
  // Для staff-режима ставка вводится вручную — не трогаем
  if (activeDbType === 'staff') return;

  const pt = document.getElementById('empPayType').value;
  if (pt === 'fixed') return;

  const posId = document.getElementById('empPosition').value;
  const actualHours = parseFloat(document.getElementById('empActualHours').value) || 0;

  if (!posId || !actualHours) {
    document.getElementById('empRate').value = '0.00';
    return;
  }

  const pos = positions.find(p => p.id == posId);
  if (pos && pos.planned_hours) {
    document.getElementById('empRate').value = (actualHours / pos.planned_hours).toFixed(2);
  }
}

// ============ ДОЛЖНОСТИ ============

async function loadPositions() {
  const res = await fetch(API_BASE + 'positions');
  positions = await res.json();
  
  // Обновляем select в форме сотрудника
  const sel = document.getElementById('empPosition');
  sel.innerHTML = '<option value=""><?php echo esc_html($T['position']); ?></option>';
  positions.forEach(p => {
    sel.innerHTML += `<option value="${p.id}">${p.name}</option>`;
  });
  
  renderPositions();
}

function renderPositions() {
  const grid = document.getElementById('posGrid');
  if (!positions.length) {
    grid.innerHTML = '<div class="empty-state"><p>Нет должностей</p></div>';
    return;
  }
  
  grid.innerHTML = positions.map(p => `
    <div class="emp-card">
      <div class="emp-info">
        <div class="emp-name">${p.name}</div>
        <div class="emp-meta">
          <span class="emp-badge badge-rate">${p.planned_hours} ч.</span>
        </div>
      </div>
      <div class="emp-actions">
        <button class="btn btn-ghost" onclick="editPosition(${p.id})"><?php echo esc_html($T['edit']); ?></button>
        <button class="btn btn-danger" onclick="deletePosition(${p.id})"><?php echo esc_html($T['delete']); ?></button>
      </div>
    </div>
  `).join('');
}

async function savePosition() {
  const data = {
    name_ru: document.getElementById('posNameRu').value.trim(),
    name_kg: document.getElementById('posNameKg').value.trim(),
    name_en: document.getElementById('posNameEn').value.trim(),
    planned_hours: parseInt(document.getElementById('posHours').value) || 0,
  };
  
  if (!data.name_ru) {
    showToast('Заполните название (RU)','warning');
    return;
  }
  
  if (editingPosId) {
    await fetch(API_BASE + `positions/${editingPosId}`, { method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data) });
    editingPosId = null;
    document.getElementById('cancelPosBtn').style.display = 'none';
  } else {
    await fetch(API_BASE + 'positions', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data) });
  }
  
  clearPosForm();
  loadPositions();
}

function editPosition(id) {
  const p = positions.find(x => x.id == id);
  if (!p) return;
  editingPosId = id;
  document.getElementById('posNameRu').value = p.name_ru;
  document.getElementById('posNameKg').value = p.name_kg;
  document.getElementById('posNameEn').value = p.name_en;
  document.getElementById('posHours').value = p.planned_hours;
  document.getElementById('cancelPosBtn').style.display = 'inline-flex';
}

async function deletePosition(id) {
  if (!confirm('Удалить должность?')) return;
  const res = await fetch(API_BASE + `positions/${id}`, { method:'DELETE' });
  const result = await res.json();
  if (!result.ok) {
    showToast(result.error || 'Ошибка при удалении','error');
    return;
  }
  loadPositions();
}

function cancelEditPos() {
  editingPosId = null;
  clearPosForm();
  document.getElementById('cancelPosBtn').style.display = 'none';
}

function clearPosForm() {
  document.getElementById('posNameRu').value = '';
  document.getElementById('posNameKg').value = '';
  document.getElementById('posNameEn').value = '';
  document.getElementById('posHours').value = '';
}


let acTimeout;
async function searchEmployees(q) {
  clearTimeout(acTimeout);
  const list = document.getElementById('nameAutocomplete');
  if (q.length < 2) { list.classList.remove('show'); return; }
  acTimeout = setTimeout(async () => {
    const res = await fetch(API_BASE + `employees/search&q=${encodeURIComponent(q)}`);
    const items = await res.json();
    if (!items.length) { list.classList.remove('show'); return; }
    list.innerHTML = items.map(i => `
      <div class="autocomplete-item" onclick="selectAutoComplete('${i.full_name}','${i.position}')">
        ${i.full_name} <small>${i.position}</small>
      </div>`).join('');
    list.classList.add('show');
  }, 200);
}

function selectAutoComplete(name, position) {
  document.getElementById('empName').value = name;
  document.getElementById('empPosition').value = position;
  document.getElementById('nameAutocomplete').classList.remove('show');
}

document.addEventListener('click', e => {
  if (!e.target.closest('.autocomplete-list') && !e.target.closest('#empName'))
    document.getElementById('nameAutocomplete').classList.remove('show');
});


async function loadTimesheet() {
  try {
    const [tsRes] = await Promise.all([
      fetch(API_BASE + `timesheet/${selectedYear}/${selectedMonth}`),
      loadWorkflowStatus()
    ]);
    if (!tsRes.ok) throw new Error(`HTTP ${tsRes.status}`);
    const data = await tsRes.json();
    renderTimesheet(data);
  } catch (error) {
    document.getElementById('tsContent').innerHTML = `<div class="empty-state"><p>Ошибка загрузки: ${error.message}</p></div>`;
  }
}

function renderTimesheet(data) {
  timesheetData = data;
  window._statusMarkMap = data.status_to_mark || {};
  const { employees: rawEmps, days, day_names } = data;
  
  // Sort: fixed/no-rate first, then alphabetical
  const emps = [...rawEmps].sort((a, b) => {
    const aFixed = (a.employee.pay_type === 'fixed' || !a.employee.rate) ? 0 : 1;
    const bFixed = (b.employee.pay_type === 'fixed' || !b.employee.rate) ? 0 : 1;
    if (aFixed !== bFixed) return aFixed - bFixed;
    return (a.employee.full_name || '').localeCompare(b.employee.full_name || '', 'ru');
  });
  const monthLabel = T[MONTH_KEYS[selectedMonth - 1]] + ' ' + selectedYear;
  document.getElementById('tsMonthLabel').textContent = monthLabel;

  if (!emps.length) {
    document.getElementById('tsContent').innerHTML = `<div class="empty-state"><p>${T.no_employees}</p></div>`;
    return;
  }

  
  const isStaff = (data.db_type === 'staff');
  
  let html = '<div class="ts-table-wrap"><table class="ts-table"><thead><tr>';
  html += `<th>${T.number}</th><th>${T.full_name}</th><th>${T.position}</th>`;
  html += `<th>${T.employment_conditions}</th>`;
  if (!isStaff) {
    html += `<th>${T.pedagog_experience || 'Стаж'}</th>`;
    html += `<th>${T.actual_hours || 'Факт.ч.'}</th>`;
  }
  html += `<th>${T.rate}</th>`;
  for (const d of days) {
    const dn = day_names[d.weekday];
    const cls = d.is_sunday ? ' sunday' : '';
    html += `<th class="day-header${cls}" onclick="openDayDropdown(event, ${d.day})" style="cursor:pointer" title="${T.click_to_change_all}">${d.day}<br>${dn}</th>`;
  }
  html += `<th>${T.working_days}</th><th>${T.weekends}</th><th>${T.days_in_month}</th>`;
  html += '</tr></thead><tbody>';

  emps.forEach((emp, idx) => {
    const e = emp.employee;
    const customClass = e.has_custom_settings ? 'has-custom' : '';
    
    // Единое поле условий работы
    let employmentLabel = '-';
    let employmentValue = null; // для определения что сейчас выбрано
    if (e.employment_internal === 'staff') {
      employmentLabel = T.staff_internal || 'Штатный';
      employmentValue = 'staff_internal';
    } else if (e.employment_internal === 'part_time') {
      employmentLabel = T.part_time_internal || 'Совм. внутр.';
      employmentValue = 'part_time_internal';
    } else if (e.employment_external === 'part_time') {
      employmentLabel = T.part_time_external || 'Совм. внешн.';
      employmentValue = 'part_time_external';
    }
    
    html += '<tr>';
    // Fire/restore - only if can manage employees
    const canManageEmp = hasPerm('can_manage_employees');
    if (canManageEmp) {
      html += `<td class="emp-number" onclick="toggleEmployeeStatus(${e.id}, \`${e.full_name}\`, ${e.is_fired || false})" style="cursor:pointer; color:var(--primary); font-weight:600;" title="${e.is_fired ? 'Восстановить' : 'Уволить'}">${idx+1}</td>`;
    } else {
      html += `<td class="emp-number">${idx+1}</td>`;
    }
    
    // FIO
    if (hasPerm('can_edit_fio')) {
      html += `<td class="name-cell-editable" contenteditable="true" data-employee-id="${e.id}" data-field="full_name" onblur="updateEmployeeName(this)">${e.full_name || ''}</td>`;
    } else {
      html += `<td>${e.full_name || ''}</td>`;
    }
    
    // Position
    if (hasPerm('can_edit_position_col')) {
      html += `<td class="pos-cell ${customClass}" onclick="openPositionDropdown(event, ${e.id})" style="cursor:pointer" title="${T.click_to_select_position}">${e.position || ''}</td>`;
    } else {
      html += `<td class="pos-cell">${e.position || ''}</td>`;
    }
    
    // Employment/conditions
    if (hasPerm('can_edit_conditions')) {
      html += `<td onclick="openEmploymentDropdown(event, ${e.id})" style="cursor:pointer; font-size:11px;" title="${T.click_to_change_employment}">${employmentLabel}</td>`;
    } else {
      html += `<td style="font-size:11px;">${employmentLabel}</td>`;
    }
    
    // Pedagog experience + Actual hours — скрыть для staff БД
    if (!isStaff) {
      // Pedagog experience
      if (e.experience_auto) {
        html += `<td style="color:var(--primary);font-weight:500" title="Авто-расчёт из стажа">${e.pedagog_experience || ''}</td>`;
      } else if (hasPerm('can_edit_experience')) {
        html += `<td contenteditable="true" data-employee-id="${e.id}" data-field="pedagog_experience" onblur="updateEmployeeField(this)" style="cursor:text">${e.pedagog_experience || ''}</td>`;
      } else {
        html += `<td>${e.pedagog_experience || ''}</td>`;
      }
    
      // Actual hours
      if (hasPerm('can_edit_hours')) {
        html += `<td contenteditable="true" data-employee-id="${e.id}" data-field="actual_hours" onblur="updateEmployeeField(this)" style="cursor:text" title="${T.actual_hours_hint}">${e.actual_hours || ''}</td>`;
      } else {
        html += `<td>${e.actual_hours || ''}</td>`;
      }
    }
    
    // Rate
    if (hasPerm('can_edit_rate')) {
      html += `<td class="rate-cell" contenteditable="true" data-employee-id="${e.id}" data-field="rate" onblur="updateEmployeeField(this)" style="cursor:text" title="${T.rate_hint}">${e.rate || 0}</td>`;
    } else {
      html += `<td class="rate-cell">${e.rate || 0}</td>`;
    }

    for (const dc of emp.day_cells) {
      const cls = `status-${dc.status}`;
      const customStyle = dc.custom_value && timesheetData.custom_marks ? (() => {
        const cm = timesheetData.custom_marks.find(m => m.symbol === dc.custom_value);
        return cm && cm.color ? `background:${cm.color}22;color:${cm.color};font-weight:600` : '';
      })() : '';
      if (hasPerm('can_edit_days')) {
        html += `<td class="day-cell ${dc.custom_value ? '' : cls}" data-emp="${e.id}" data-day="${dc.day}" ${customStyle ? `style="${customStyle}"` : ''}>${dc.display}</td>`;
      } else {
        html += `<td class="day-cell ${dc.custom_value ? '' : cls}" ${customStyle ? `style="${customStyle};cursor:default"` : 'style="cursor:default"'}>${dc.display}</td>`;
      }
    }

    html += `<td class="summary-cell">${emp.working_days}</td>`;
    html += `<td class="summary-cell">${emp.weekends}</td>`;
    html += `<td class="summary-cell">${emp.total_days}</td>`;
    html += '</tr>';
  });

  html += '</tbody></table></div>';
  
  // Workflow status bar
  const blocked = isWorkflowBlocked();
  if (wfCurrentStatus && wfCurrentStatus.chains && wfCurrentStatus.chains.length) {
    const statusMap = {draft:'⬜ Черновик',in_progress:'🔄 В процессе',revision:'🔙 На доработке',completed:'✅ Завершён'};
    const st = wfCurrentStatus.status || 'draft';
    const curStep = wfCurrentStatus.current_step;
    const curChain = wfCurrentStatus.chains[curStep];
    const isCompleted = st === 'completed';
    
    let bgStyle = blocked || isCompleted ? 'background:#fff3e0;border:1px solid #FFB74D' : 'background:#e8f5e9;border:1px solid #81C784';
    if (isCompleted) bgStyle = 'background:#e8f5e9;border:1px solid #81C784';
    
    let wfHtml = `<div style="padding:10px 14px;margin-bottom:8px;border-radius:var(--radius);display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:13px;${bgStyle}">
      <span style="font-weight:700">${statusMap[st]||st}</span>`;
    
    if (isCompleted) {
      wfHtml += `<span style="color:#2e7d32">🔒 Табель согласован и заблокирован</span>`;
    } else if (curChain && wfCurrentStatus.activated) {
      wfHtml += `<span>Этап: <b>${curChain.step_name}</b></span>`;
    }
    
    if (blocked && !isCompleted && !meIsAdmin) {
      wfHtml += `<span style="color:#E65100">🔒 Только чтение — ожидайте вашей очереди</span>`;
    }
    
    if (canSubmitWorkflow() && !meIsAdmin) {
      wfHtml += `<button class="btn btn-primary" style="margin-left:auto;font-size:12px" onclick="workflowSubmit()">📤 Отправить дальше</button>`;
    }
    
    if (!isCompleted && canReturnWorkflow()) {
      const returnBtns = wfCurrentStatus.chains
        .filter((c, i) => i < curStep && !c.is_reviewer)
        .map(c => `<button class="btn btn-ghost" style="font-size:11px;color:var(--danger)" onclick="workflowReturn(${c.step_order})">🔙 ${c.step_name}</button>`).join('');
      if (returnBtns) wfHtml += returnBtns;
    }
    
    wfHtml += '</div>';
    html = wfHtml + html;
  }

  document.getElementById('tsContent').innerHTML = html;
  
  // Block all editing if workflow blocked
  if (blocked && !meIsAdmin) {
    document.querySelectorAll('#tsContent [contenteditable]').forEach(el => {
      el.contentEditable = 'false';
      el.style.cursor = 'default';
    });
    document.querySelectorAll('#tsContent .day-cell[data-emp]').forEach(el => {
      el.removeAttribute('data-emp');
      el.removeAttribute('data-day');
      el.style.cursor = 'default';
    });
    document.querySelectorAll('#tsContent .pos-cell[onclick], #tsContent td[onclick], #tsContent .emp-number[onclick]').forEach(el => {
      el.removeAttribute('onclick');
      el.style.cursor = 'default';
    });
  }
  
  // Init drag selection for cells
  initDragSelect();

  // Inject custom marks into cell/day dropdowns
  updateCustomMarksInDropdowns(data.custom_marks || []);

  const expDiv = document.getElementById('exportByEmployee');
  expDiv.innerHTML = '<div style="border-top:1px solid var(--border); margin-top:4px; padding-top:4px; font-size:11px; color:var(--text-tertiary); padding-left:12px;">' + T.export_employee + '</div>';
  emps.forEach(emp => {
    expDiv.innerHTML += `<a href="#" onclick="exportEmployee(${emp.employee.id}); return false;">${emp.employee.full_name}</a>`;
  });
}


let dayDropdownTarget = null;

function openDayDropdown(event, day) {
  if (isWorkflowBlocked() && !meIsAdmin) return;
  event.stopPropagation();
  dayDropdownTarget = day;
  const dd = document.getElementById('dayDropdown');
  dd.classList.add('show');
  const rect = event.target.getBoundingClientRect();
  positionDropdown(dd, rect);
}

document.addEventListener('click', e => {
  if (!e.target.closest('#dayDropdown') && !e.target.closest('.day-header')) {
    const dd = document.getElementById('dayDropdown');
    if (dd) dd.classList.remove('show');
  }
});

async function setDayStatusForAll(status) {
  if (dayDropdownTarget === null) return;
  document.getElementById('dayDropdown').classList.remove('show');
  
  document.querySelectorAll(`td.day-cell[data-day="${dayDropdownTarget}"]`).forEach(cell => {
    const empId = Number(cell.dataset.emp);
    cell.textContent = getCellDisplay(status, empId, dayDropdownTarget);
    cell.className = 'day-cell status-' + status;
  });
  
  try {
    const res = await fetch(API_BASE + 'timesheet/bulk', {
      method: 'POST', headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ year: selectedYear, month: selectedMonth, day: dayDropdownTarget, status })
    });
    if (!res.ok) throw new Error();
  } catch(e) {
    showToast('❌ Ошибка сохранения', 'error');
    loadTimesheet();
  }
}


function saveScrollPosition() {
  const wrap = document.querySelector('.ts-table-wrap');
  if (!wrap) return null;
  return {
    scrollLeft: wrap.scrollLeft,
    scrollTop: wrap.scrollTop
  };
}

function restoreScrollPosition(pos) {
  if (!pos) return;
  const wrap = document.querySelector('.ts-table-wrap');
  if (!wrap) return;
  wrap.scrollLeft = pos.scrollLeft;
  wrap.scrollTop = pos.scrollTop;
}


// Умное позиционирование дропдауна — открывается вверх если не помещается вниз
function positionDropdown(dd, rect) {
  dd.style.left = '';
  dd.style.top = '';
  dd.style.bottom = '';
  dd.style.right = '';
  dd.style.maxHeight = '';
  const ddW = dd.offsetWidth || 180;
  const vw = window.innerWidth;
  const vh = window.innerHeight;
  const margin = 8;
  const spaceBelow = vh - rect.bottom - margin;
  const spaceAbove = rect.top - margin;
  // Открыть вверх если снизу меньше места И сверху больше
  if (spaceBelow < 200 && spaceAbove > spaceBelow) {
    const availH = Math.min(spaceAbove, vh * 0.7);
    dd.style.maxHeight = availH + 'px';
    dd.style.top = (rect.top - availH - 4) + 'px';
  } else {
    const availH = Math.min(spaceBelow, vh * 0.7);
    dd.style.maxHeight = availH + 'px';
    dd.style.top = (rect.bottom + 4) + 'px';
  }
  // Горизонталь: если уходит за правый край — сдвинуть влево
  const left = Math.min(rect.left, vw - ddW - margin);
  dd.style.left = Math.max(4, left) + 'px';
}

function openCellDropdown(event, empId, day) {
  if (isWorkflowBlocked() && !meIsAdmin) return;
  event.stopPropagation();
  dropdownTarget = { employeeId: empId, day };
  const dd = document.getElementById('cellDropdown');
  dd.classList.add('show');
  const rect = event.target.getBoundingClientRect();
  positionDropdown(dd, rect);
  document.getElementById('customCellValue').value = '';
}

document.addEventListener('click', e => {
  if (dragJustFinished) return;
  if (!e.target.closest('.cell-dropdown') && !e.target.closest('.day-cell')) {
    document.getElementById('cellDropdown').classList.remove('show');
    clearDragSelection();
    bulkTargets = [];
  }
});

// Status-to-color map for custom mark dots
const MARK_STATUS_COLORS = {
  work:'#4CAF50', day_off:'#E91E63', holiday:'#FF9800', sick:'#2196F3',
  vacation:'#9C27B0', business_trip:'#FF9800', maternity:'#E91E63',
  childcare:'#FF5722', study_leave:'#607D8B', admin_leave:'#795548',
  absence:'#F44336', late:'#FF6F00', early_leave:'#BF360C'
};

// Standard mark symbols that are already in the hardcoded dropdown
const STANDARD_SYMBOLS = new Set(['К','О','С','КӨ','БӨ','ОӨ','АӨ','Дк','Кк','Эк']);

function updateCustomMarksInDropdowns(marks) {
  // Always remove old custom marks first
  document.querySelectorAll('.custom-mark-item, .custom-mark-divider').forEach(el => el.remove());
  
  if (!marks || !marks.length) return;
  
  // Build a color map from ALL marks (including standard ones for color override)
  const markColorMap = {};
  marks.forEach(m => { if (m.symbol && m.color) markColorMap[m.symbol.trim()] = m.color; });
  
  // Update colors of standard buttons in cellDropdown and dayDropdown
  document.querySelectorAll('#cellDropdown button[onclick*="setCellStatus"], #dayDropdown button[onclick*="setDayStatusForAll"]').forEach(btn => {
    const match = btn.textContent.match(/\(([^)]+)\)\s*$/);
    if (match && markColorMap[match[1]]) {
      const dot = btn.querySelector('.dot');
      if (dot) dot.style.background = markColorMap[match[1]];
    }
  });
  
  // Only show marks that are NOT already standard
  const newMarks = marks.filter(m => m.symbol && !STANDARD_SYMBOLS.has(m.symbol.trim()));
  if (!newMarks.length) return;
  
  const cellDD = document.getElementById('cellDropdown');
  const dayDD = document.getElementById('dayDropdown');
  const dividerText = T.es_marks_panel || 'Условные обозначения';
  
  const markBtns = newMarks.map(m => ({
    symbol: m.symbol,
    color: m.color || '#9E9E9E',
    label: m.symbol + (m.description ? ' — ' + m.description : ''),
  }));

  // Insert into cellDropdown before the custom-input-row (no extra divider)
  const customInput = cellDD.querySelector('.custom-input-row');
  if (customInput) {
    markBtns.forEach(m => {
      const btn = document.createElement('button');
      btn.className = 'custom-mark-item';
      btn.innerHTML = `<span class="dot" style="background:${m.color}"></span>${m.label}`;
      btn.onclick = () => setCellMarkCustom('work', m.symbol);
      customInput.before(btn);
    });
  }
  
  // Insert into dayDropdown at the end (no extra divider)
  if (dayDD) {
    markBtns.forEach(m => {
      const btn = document.createElement('button');
      btn.className = 'custom-mark-item';
      btn.innerHTML = `<span class="dot" style="background:${m.color}"></span>${m.label}`;
      btn.onclick = () => setDayMarkCustomForAll('work', m.symbol);
      dayDD.appendChild(btn);
    });
  }
}

async function setCellMarkCustom(status, symbol) {
  if (!dropdownTarget) return;
  const scrollPos = saveScrollPosition();
  await fetch(API_BASE + 'timesheet/entry', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      employee_id: dropdownTarget.employeeId,
      year: selectedYear, month: selectedMonth, day: dropdownTarget.day,
      status: status, custom_value: symbol,
    })
  });
  document.getElementById('cellDropdown').classList.remove('show');
  await loadTimesheet();
  restoreScrollPosition(scrollPos);
}

async function setDayMarkCustomForAll(status, symbol) {
  const scrollPos = saveScrollPosition();
  await fetch(API_BASE + 'timesheet/bulk', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      year: selectedYear, month: selectedMonth, day: dayDropdownTarget,
      status: status, custom_value: symbol,
    })
  });
  document.getElementById('dayDropdown').classList.remove('show');
  await loadTimesheet();
  restoreScrollPosition(scrollPos);
}

const DEFAULT_MARKS = {
  vacation:'К', sick:'О', business_trip:'С', maternity:'КӨ', childcare:'БӨ',
  study_leave:'ОӨ', admin_leave:'АӨ', absence:'Дк', late:'Кк', early_leave:'Эк'
};

function getCellDisplay(status, employeeId, day) {
  const MK = window._statusMarkMap || {};
  if (status === 'day_off') return '-';
  if (status !== 'work') return MK[status] || DEFAULT_MARKS[status] || status;
  if (!timesheetData || !timesheetData.employees) return '+';
  const empData = timesheetData.employees.find(e => e.employee && e.employee.id == employeeId);
  if (!empData || !empData.employee) return '+';
  const e = empData.employee;
  const rate = parseFloat(e.rate);
  if (!rate || rate <= 0 || e.pay_type === 'fixed') return '+';
  const dbType = timesheetData.db_type || 'pps';
  if (dbType === 'staff') {
    const dayInfo = timesheetData.days ? timesheetData.days.find(d => d.day == day) : null;
    const wd = dayInfo ? dayInfo.weekday : 0;
    return String(round2(rate * (wd === 5 ? 5 : 7)));
  }
  return String(round2(rate * 6));
}

function round2(n) { return Math.round(n * 100) / 100; }

async function setCellStatus(status) {
  // Bulk mode
  if (bulkTargets && bulkTargets.length > 0) { await setCellStatusBulk(status); return; }
  
  if (!dropdownTarget) return;
  const {employeeId, day} = dropdownTarget;
  document.getElementById('cellDropdown').classList.remove('show');
  
  const cell = document.querySelector(`td.day-cell[data-emp="${employeeId}"][data-day="${day}"]`);
  const prevText = cell ? cell.textContent : '';
  const prevClass = cell ? cell.className : '';
  if (cell) {
    cell.textContent = getCellDisplay(status, employeeId, day);
    cell.className = 'day-cell status-' + status;
  }
  
  try {
    const res = await fetch(API_BASE + 'timesheet/entry', {
      method: 'POST', headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({employee_id: employeeId, year: selectedYear, month: selectedMonth, day, status})
    });
    if (!res.ok) throw new Error();
  } catch(e) {
    if (cell) { cell.textContent = prevText; cell.className = prevClass; }
    showToast('❌ Ошибка сохранения', 'error');
  }
}

async function setCellCustom() {
  const val = document.getElementById('customCellValue').value.trim();
  if (!val) return;
  
  // Bulk mode
  if (bulkTargets && bulkTargets.length > 0) {
    document.getElementById('cellDropdown').classList.remove('show');
    bulkTargets.forEach(t => {
      const cell = document.querySelector(`td.day-cell[data-emp="${t.emp}"][data-day="${t.day}"]`);
      if (cell) { cell.textContent = val; cell.className = 'day-cell status-work'; }
    });
    const cells = [...bulkTargets];
    clearDragSelection(); bulkTargets = [];
    try {
      const res = await fetch(API_BASE + 'timesheet/batch', {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({cells, year: selectedYear, month: selectedMonth, status: 'work', custom_value: val})
      });
      if (!res.ok) throw new Error();
    } catch(e) { showToast('❌ Ошибка', 'error'); loadTimesheet(); }
    return;
  }
  
  if (!dropdownTarget) return;
  const {employeeId, day} = dropdownTarget;
  document.getElementById('cellDropdown').classList.remove('show');
  
  const cell = document.querySelector(`td.day-cell[data-emp="${employeeId}"][data-day="${day}"]`);
  const prevText = cell ? cell.textContent : '';
  const prevClass = cell ? cell.className : '';
  if (cell) { cell.textContent = val; cell.className = 'day-cell status-work'; }
  
  try {
    const res = await fetch(API_BASE + 'timesheet/entry', {
      method: 'POST', headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({employee_id: employeeId, year: selectedYear, month: selectedMonth, day, status: 'work', custom_value: val})
    });
    if (!res.ok) throw new Error();
  } catch(e) {
    if (cell) { cell.textContent = prevText; cell.className = prevClass; }
    showToast('❌ Ошибка сохранения', 'error');
  }
}

async function sundayBulk(status) {
  document.getElementById('dayDropdown').classList.remove('show');
  await fetch(API_BASE + 'timesheet/sunday_bulk', {
    method: 'POST', headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ year: selectedYear, month: selectedMonth, status })
  });
  loadTimesheet();
}


async function updateMonthlyField(cell) {
  const employeeId = parseInt(cell.dataset.employeeId);
  const field = cell.dataset.field;
  const value = cell.textContent.trim();
  
  if (!value) return;
  
  const data = {
    employee_id: employeeId,
    year: selectedYear,
    month: selectedMonth,
  };
  
  if (field === 'position') {
    data.position = value;
    data.rate = null; 
  } else if (field === 'rate') {
    const numValue = parseFloat(value);
    if (isNaN(numValue)) {
      showToast('Ставка должна быть числом','warning');
      loadTimesheet();
      return;
    }
    data.rate = numValue;
    data.position = null; 
  }
  
  await fetch(API_BASE + 'monthly_settings', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  });
  
  loadTimesheet();
}


// Переменная для хранения данных о выбираемой должности
let positionDropdownTarget = null;

// Открыть выпадающее меню для выбора должности
function openPositionDropdown(event, employeeId) {
  if (isWorkflowBlocked() && !meIsAdmin) return;
  event.stopPropagation();
  
  // Находим текущую должность сотрудника из данных табеля
  let currentPosition = '';
  if (timesheetData && timesheetData.employees) {
    const empData = timesheetData.employees.find(e => e.employee.id == employeeId);
    if (empData) {
      currentPosition = empData.employee.position || '';
    }
  }
  
  positionDropdownTarget = { employeeId, currentPosition };
  
  const dd = document.getElementById('positionDropdown');
  const content = document.getElementById('positionDropdownContent');
  
  // Формируем список должностей
  let html = '';
  positions.forEach(pos => {
    const isSelected = pos.name === currentPosition ? ' style="background:var(--surface2)"' : '';
    const safeName = pos.name.replace(/'/g, "\\'");
    html += `<button onclick="setEmployeePosition('${safeName}')"${isSelected}>${pos.name}</button>`;
  });
  content.innerHTML = html;
  
  dd.classList.add('show');
  const rect = event.target.getBoundingClientRect();
  positionDropdown(dd, rect);
}

// Закрытие dropdown при клике вне его
document.addEventListener('click', e => {
  if (!e.target.closest('#positionDropdown') && !e.target.closest('.pos-cell')) {
    document.getElementById('positionDropdown').classList.remove('show');
  }
});

// Установить выбранную должность для сотрудника
async function setEmployeePosition(positionName) {
  if (!positionDropdownTarget) return;
  
  const scrollPos = saveScrollPosition();
  
  // Находим выбранную должность чтобы получить planned_hours
  const selectedPos = positions.find(p => p.name === positionName);
  
  // Находим сотрудника чтобы получить actual_hours и pay_type
  let actualHours = null;
  let payType = null;
  if (timesheetData && timesheetData.employees) {
    const empData = timesheetData.employees.find(e => e.employee.id == positionDropdownTarget.employeeId);
    if (empData) {
      actualHours = empData.employee.actual_hours;
      payType = empData.employee.pay_type;
    }
  }
  
  // Рассчитываем новую ставку ТОЛЬКО если pay_type = 'rate' и есть оба значения
  let newRate = null;
  if (payType === 'rate' && selectedPos && selectedPos.planned_hours && actualHours) {
    newRate = Math.round((actualHours / selectedPos.planned_hours) * 100) / 100;
  }
  
  // Отправляем запрос с обновлением должности и должности ID
  await fetch(API_BASE + 'employee/update_position', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      employee_id: positionDropdownTarget.employeeId,
      year: selectedYear,
      month: selectedMonth,
      position: positionName,
      position_id: selectedPos ? selectedPos.id : null,
      rate: newRate
    })
  });
  
  document.getElementById('positionDropdown').classList.remove('show');
  await loadTimesheet();
  restoreScrollPosition(scrollPos);
}

// Уволить или восстановить сотрудника
async function toggleEmployeeStatus(employeeId, employeeName, isFired) {
  if (isWorkflowBlocked() && !meIsAdmin) { showToast('🔒 Табель заблокирован','warning'); return; }
  // Находим имя сотрудника из данных табеля
  let name = '';
  if (timesheetData && timesheetData.employees) {
    const empData = timesheetData.employees.find(e => e.employee.id == employeeId);
    if (empData) {
      name = empData.employee.full_name || '';
    }
  }
  
  const action = isFired ? 'восстановить' : 'уволить';
  const message = `Вы уверены, что хотите ${action} сотрудника "${name}"?`;
  
  if (!confirm(message)) return;
  
  const scrollPos = saveScrollPosition();
  
  // Отправляем запрос на увольнение/восстановление
  await fetch(API_BASE + 'employee/fire', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      employee_id: employeeId,
      year: selectedYear,
      month: selectedMonth,
      fire: !isFired  // Инвертируем статус
    })
  });
  
  await loadTimesheet();
  restoreScrollPosition(scrollPos);
}

// Переменная для типа занятости
let employmentDropdownTarget = null;

// Открыть единое выпадающее меню для условий работы
function openEmploymentDropdown(event, employeeId) {
  if (isWorkflowBlocked() && !meIsAdmin) return;
  event.stopPropagation();
  
  // Находим текущие значения
  let currentInternal = null;
  let currentExternal = null;
  if (timesheetData && timesheetData.employees) {
    const empData = timesheetData.employees.find(e => e.employee.id == employeeId);
    if (empData) {
      currentInternal = empData.employee.employment_internal;
      currentExternal = empData.employee.employment_external;
    }
  }
  
  employmentDropdownTarget = { employeeId };
  
  const dd = document.getElementById('employmentDropdown');
  const content = document.getElementById('employmentDropdownContent');
  
  // Определяем текущий выбор
  const isStaffInt = currentInternal === 'staff';
  const isPartInt = currentInternal === 'part_time';
  const isPartExt = currentExternal === 'part_time';
  const isNone = !currentInternal && !currentExternal;
  
  let html = '';
  html += `<button onclick="setEmploymentUnified(null, null)" ${isNone ? 'style="background:var(--surface2)"' : ''}>— ${T.not_selected || 'Тандалган эмес'}</button>`;
  html += `<button onclick="setEmploymentUnified('staff', null)" ${isStaffInt ? 'style="background:var(--surface2)"' : ''}>${T.staff_internal || 'Штатный'}</button>`;
  html += `<button onclick="setEmploymentUnified('part_time', null)" ${isPartInt ? 'style="background:var(--surface2)"' : ''}>${T.part_time_internal || 'Совм. внутр.'}</button>`;
  html += `<button onclick="setEmploymentUnified(null, 'part_time')" ${isPartExt ? 'style="background:var(--surface2)"' : ''}>${T.part_time_external || 'Совм. внешн.'}</button>`;
  
  content.innerHTML = html;
  
  dd.classList.add('show');
  const rect = event.target.getBoundingClientRect();
  positionDropdown(dd, rect);
}

// Закрытие dropdown типа занятости
document.addEventListener('click', e => {
  if (!e.target.closest('#employmentDropdown')) {
    document.getElementById('employmentDropdown').classList.remove('show');
  }
});

// Установить условия работы (всегда отправляем оба поля, одно заполнено, другое null)
async function setEmploymentUnified(internalValue, externalValue) {
  if (!employmentDropdownTarget) return;
  
  const scrollPos = saveScrollPosition();
  
  const data = {
    employee_id: employmentDropdownTarget.employeeId,
    year: selectedYear,
    month: selectedMonth,
    employment_internal: internalValue,
    employment_external: externalValue
  };
  
  await fetch(API_BASE + 'employee/update_employment', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  });
  
  document.getElementById('employmentDropdown').classList.remove('show');
  await loadTimesheet();
  restoreScrollPosition(scrollPos);
}

// Обновить поле сотрудника (фактические часы или пед.стаж)
async function updateEmployeeField(cell) {
  if (isWorkflowBlocked() && !meIsAdmin) { loadTimesheet(); return; }
  const employeeId = parseInt(cell.dataset.employeeId);
  const field = cell.dataset.field;
  const value = cell.textContent.trim();
  
  const scrollPos = saveScrollPosition();
  
  const data = {
    employee_id: employeeId,
    year: selectedYear,
    month: selectedMonth
  };
  
  if (field === 'actual_hours') {
    const numValue = parseFloat(value);
    if (isNaN(numValue) && value !== '') {
      showToast('Факт. часы должны быть числом','warning');
      await loadTimesheet();
      restoreScrollPosition(scrollPos);
      return;
    }
    data.actual_hours = value === '' ? null : numValue;
    
    // Пересчитываем ставку ТОЛЬКО если pay_type = 'rate' (не fixed!)
    if (timesheetData && timesheetData.employees) {
      const empData = timesheetData.employees.find(e => e.employee.id == employeeId);
      // Проверяем что это сотрудник с оплатой по ставке, а не фиксированной суммой
      if (empData && empData.employee.pay_type === 'rate' && empData.employee.position_id && numValue) {
        const pos = positions.find(p => p.id == empData.employee.position_id);
        if (pos && pos.planned_hours) {
          data.rate = Math.round((numValue / pos.planned_hours) * 100) / 100;
        }
      }
    }
  } else if (field === 'rate') {
    const numValue = parseFloat(value);
    if (isNaN(numValue) && value !== '') {
      showToast('Ставка должна быть числом','warning');
      await loadTimesheet();
      restoreScrollPosition(scrollPos);
      return;
    }
    data.rate = value === '' ? null : numValue;
  } else if (field === 'pedagog_experience') {
    data.pedagog_experience = value || null;
  }
  
  await fetch(API_BASE + 'employee/update_field', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  });
  
  await loadTimesheet();
  restoreScrollPosition(scrollPos);
}


// Обновить имя сотрудника из таблицы
async function updateEmployeeName(cell) {
  if (isWorkflowBlocked() && !meIsAdmin) { loadTimesheet(); return; }
  const employeeId = parseInt(cell.dataset.employeeId);
  const newName = cell.textContent.trim();
  
  if (!newName) {
    await loadTimesheet();
    return;
  }
  
  const scrollPos = saveScrollPosition();
  
  await fetch(API_BASE + `employees/${employeeId}/name`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ 
      full_name: newName,
      year: selectedYear,
      month: selectedMonth
    })
  });
  
  await loadTimesheet();
  restoreScrollPosition(scrollPos);
}


// ===== EXCEL НАСТРОЙКИ =====
// ===== STATUS OPTIONS for mark dropdown =====
const STATUS_OPTIONS = [
  {value: '', label: '— ' + (T.not_selected || 'не выбрано') + ' —'},
  {value: 'vacation',      label: '🟣 ' + (T.vacation || 'Каникулы') + ' (К)'},
  {value: 'sick',          label: '🔵 ' + (T.sick || 'Больничный') + ' (О)'},
  {value: 'business_trip', label: '🟠 ' + (T.business_trip || 'Командировка') + ' (С)'},
  {value: 'maternity',     label: '🩷 ' + (T.maternity || 'Декрет') + ' (КӨ)'},
  {value: 'childcare',     label: '🟤 ' + (T.childcare || 'Уход за ребенком') + ' (БӨ)'},
  {value: 'study_leave',   label: '⬜ ' + (T.study_leave || 'Учебный') + ' (ОӨ)'},
  {value: 'admin_leave',   label: '🟫 ' + (T.admin_leave || 'Адм. отпуск') + ' (АӨ)'},
  {value: 'absence',       label: '🔴 ' + (T.absence || 'Неявка') + ' (Дк)'},
  {value: 'late',          label: '🟡 ' + (T.late || 'Опоздание') + ' (Кк)'},
  {value: 'early_leave',   label: '🟠 ' + (T.early_leave || 'Ранний уход') + ' (Эк)'},
];

let excelSettings = {};
let marksList = []; // [{symbol, status, description}, ...]

function renderMarksList() {
  const list = document.getElementById('marksSettingsList');
  if (!list) return;
  
  if (marksList.length === 0) {
    list.innerHTML = `
      <div style="padding:16px 0;color:var(--text-tertiary);font-size:13px;text-align:center">
        ${T.es_add_mark ? T.es_add_mark + ' ↓' : 'Нет обозначений'}
      </div>`;
    return;
  }
  
  list.innerHTML = marksList.map((m, idx) => {
    const isStandard = STANDARD_SYMBOLS.has((m.symbol||'').trim());
    const color = m.color || MARK_STATUS_COLORS[m.status] || '#9E9E9E';
    return `
    <div class="es-mark-row" data-idx="${idx}" style="display:grid;grid-template-columns:36px 70px 1fr 36px;gap:8px;align-items:center;padding:6px 0;border-bottom:1px solid var(--border)">
      <input type="color" class="mk-color" value="${color}" oninput="marksList[${idx}].color=this.value"
             style="width:32px;height:32px;padding:0;border:1px solid var(--border);border-radius:var(--radius);cursor:pointer;background:none">
      <input type="text" class="mk-symbol" value="${(m.symbol||'').replace(/"/g,'&quot;')}" placeholder="К"
             maxlength="4" oninput="marksList[${idx}].symbol=this.value"
             style="text-align:center;font-weight:700;font-size:16px;padding:6px 4px;border:1px solid var(--border);border-radius:var(--radius);width:100%">
      <input type="text" class="mk-desc" value="${(m.description||'').replace(/"/g,'&quot;')}" placeholder="${T.es_description || 'Описание'}..."
             oninput="marksList[${idx}].description=this.value"
             style="padding:6px 10px;border:1px solid var(--border);border-radius:var(--radius);font-family:inherit;font-size:13px;width:100%">
      <button onclick="deleteMark(${idx})" title="Удалить"
              style="padding:5px 9px;background:#ffebe9;border:1px solid #ffadb5;border-radius:var(--radius);cursor:pointer;font-size:14px;color:var(--danger);font-weight:700">✕</button>
    </div>`;
  }).join('');
}

function addMark() {
  marksList.push({symbol: '', status: '', description: '', color: '#9E9E9E'});
  renderMarksList();
}

function deleteMark(idx) {
  marksList.splice(idx, 1);
  renderMarksList();
}

async function loadExcelSettings() {
  try {
    const res = await fetch(API_BASE + 'excel_settings');
    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      if (err.error === 'no_db') {
        document.getElementById('marksSettingsList').innerHTML =
          '<p style="color:var(--text-secondary);font-size:13px;padding:8px 0">⚠️ Сначала выберите базу данных</p>';
      }
      return;
    }
    excelSettings = await res.json();
    
    // Fill header fields
    ['header_left1','header_left2','header_left3',
     'header_center1','header_center2','header_center3',
     'header_right1','header_right2','header_right3',
     'sig1','sig2','sig3','doc_title'].forEach(key => {
      const el = document.getElementById('s_' + key);
      if (el && excelSettings[key] !== undefined) el.value = excelSettings[key];
    });
    
    // Load marks list
    if (Array.isArray(excelSettings.marks_list)) {
      marksList = JSON.parse(JSON.stringify(excelSettings.marks_list));
    } else {
      marksList = [];
    }
    renderMarksList();
  } catch(e) {
    console.error('loadExcelSettings error:', e);
  }
}

async function saveExcelSettings() {
  const data = {};
  ['header_left1','header_left2','header_left3',
   'header_center1','header_center2','header_center3',
   'header_right1','header_right2','header_right3',
   'sig1','sig2','sig3','doc_title'].forEach(key => {
    const el = document.getElementById('s_' + key);
    if (el) data[key] = el.value;
  });
  
  // Sync marks from DOM to marksList
  document.querySelectorAll('.es-mark-row').forEach((row, idx) => {
    const sym = row.querySelector('.mk-symbol');
    const desc = row.querySelector('.mk-desc');
    const col = row.querySelector('.mk-color');
    if (sym && marksList[idx] !== undefined) {
      marksList[idx].symbol = sym.value;
      marksList[idx].description = desc ? desc.value : '';
      marksList[idx].color = col ? col.value : '';
    }
  });
  
  data.marks_list = marksList;
  
  const res = await fetch(API_BASE + 'excel_settings', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  });
  
  const btn = document.getElementById('excelSaveBtn');
  if (res.ok) {
    const orig = btn.innerHTML;
    btn.innerHTML = '✅ Сакталды!';
    btn.disabled = true;
    setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2000);
  } else {
    btn.innerHTML = '❌ Ката';
    setTimeout(() => { btn.innerHTML = '💾 Баарын сактоо'; btn.disabled = false; }, 2000);
  }
}

function toggleExportMenu() {
  document.getElementById('exportDropdown').classList.toggle('show');
}
document.addEventListener('click', e => {
  if (!e.target.closest('.export-menu'))
    document.getElementById('exportDropdown').classList.remove('show');
});

function exportFull() {
  window.open(EXPORT_BASE + `full/${selectedYear}/${selectedMonth}`, '_blank');
}
function exportBrief() {
  window.open(EXPORT_BASE + `brief/${selectedYear}/${selectedMonth}`, '_blank');
}
function exportEmployee(eid) {
  window.open(EXPORT_BASE + `employee/${eid}/${selectedYear}/${selectedMonth}`, '_blank');
}


document.getElementById('monthSelect').value = selectedMonth;
document.getElementById('yearInput').value = selectedYear;

// ===== INIT: load current user & permissions =====
const PERM_LABELS = {
  can_manage_databases: '🗄️ Управление базами данных',
  can_view_stats: '📊 Просмотр статистики',
  can_view_history: '🕐 История экспортов',
  can_export_excel: '📥 Скачивание Excel',
  can_fire_employee: '🚫 Увольнение сотрудников',
  can_manage_employees: '👤 Управление сотрудниками',
  can_manage_positions: '💼 Управление должностями',
  can_edit_excel: '📊 Редактировать Excel / Заголовки',
  can_edit_fio: '✏️ Редактировать ФИО в табеле',
  can_edit_position_col: '💼 Менять должность в табеле',
  can_edit_conditions: '📋 Менять шарттары в табеле',
  can_edit_experience: '🎓 Менять стаж в табеле',
  can_edit_hours: '⏱️ Менять факт.часы в табеле',
  can_edit_rate: '💰 Менять ставку в табеле',
  can_edit_days: '📅 Редактировать дни табеля',
  can_manage_experience: '🕐 Управление стажем',
};

let accessibleDbs = [];
let activeDbName = null;
let activeDbType = 'pps'; // 'pps' or 'staff'

async function initMe() {
  try {
    const res = await fetch(API_BASE + 'me');
    if (res.status === 401) { window.location.reload(); return; }
    const me = await res.json();
    if (!me.logged_in) { window.location.reload(); return; }
    
    meIsAdmin = me.is_superadmin;
    meUserId = me.user_id;
    mePerms = me.perms || {};
    accessibleDbs = me.accessible_dbs || [];
    activeDbName = me.active_db || null;
    const activeDbObj = accessibleDbs.find(d => d.name === activeDbName);
    activeDbType = activeDbObj ? (activeDbObj.db_type || 'pps') : 'pps';
    
    // Update user bar
    const uname = me.username || '?';
    document.getElementById('sidebarAvatar').textContent = uname[0].toUpperCase();
    document.getElementById('sidebarUsername').textContent = uname;
    document.getElementById('sidebarRole').textContent = meIsAdmin ? '⭐ Суперадмин' : 'Пользователь';
    
    // Show privileged nav items
    if (meIsAdmin) {
      document.getElementById('nav-users').style.display = '';
      document.getElementById('nav-databases').style.display = '';
      document.getElementById('nav-statistics').style.display = '';
      document.getElementById('nav-history').style.display = '';
      document.getElementById('nav-workflow').style.display = '';
      document.getElementById('nav-experience').style.display = '';
    } else {
      if (mePerms.can_manage_databases) document.getElementById('nav-databases').style.display = '';
      if (mePerms.can_view_stats) document.getElementById('nav-statistics').style.display = '';
      if (mePerms.can_view_history) document.getElementById('nav-history').style.display = '';
      if (mePerms.can_manage_employees) document.getElementById('nav-experience').style.display = '';
      if (mePerms.can_manage_experience) document.getElementById('nav-experience').style.display = '';
    }
    
    // Load notification count
    pollNotifications();
    
    // Render DB switcher
    renderDbSwitcher();
    initStatSelectors();
    
    // Apply permission visibility restrictions
    applyPermUI();
    // Apply db type UI (pps vs staff)
    applyDbTypeModeUI();
    
    // Show gentle banner if no db, but don't block
    updateNoDbBanner();
    
    // Load data only if we have an active db
    if (activeDbName) {
      loadTimesheet();
      loadEmployees();
      loadPositions();
    }
    
  } catch(e) {
    console.error('Auth check failed', e);
  }
}

function updateNoDbBanner() {
  const banner = document.getElementById('noDbBanner');
  if (!banner) return;
  if (!activeDbName) {
    banner.style.display = 'flex';
    const canCreate = meIsAdmin || mePerms.can_manage_databases;
    const actionBtn = canCreate
      ? `<button class="btn btn-primary" style="margin-left:auto;font-size:12px" onclick="showPage('databases')">＋ Создать / выбрать БД</button>`
      : `<span style="margin-left:auto;color:var(--text-secondary);font-size:12px">Обратитесь к администратору</span>`;
    banner.innerHTML = `<span>⚠️ База данных не выбрана.</span>${actionBtn}`;
  } else {
    banner.style.display = 'none';
  }
}


function applyDbTypeModeUI() {
  // Hide/show pps-only fields (actual_hours, pedagog_experience, planned_hours)
  const isStaffMode = (activeDbType === 'staff');
  document.querySelectorAll('.pps-only-field').forEach(el => {
    el.style.display = isStaffMode ? 'none' : '';
  });
  // Update DB type badge in switcher
  const badge = document.getElementById('dbTypeBadge');
  if (badge) {
    badge.textContent = isStaffMode ? '👷 Сотр.' : '🎓 ППС';
    badge.style.background = isStaffMode ? '#fff8c5' : '#ddf4ff';
    badge.style.color = isStaffMode ? '#7a4700' : 'var(--primary)';
  }
}
// ===== DB SWITCHER =====
function renderDbSwitcher() {
  const nameEl = document.getElementById('dbActiveName');
  const dropdown = document.getElementById('dbDropdown');
  
  if (activeDbName) {
    const activeDb = accessibleDbs.find(d => d.name === activeDbName);
    nameEl.textContent = activeDb ? activeDb.display_name : activeDbName;
  } else {
    nameEl.textContent = 'Не выбрана';
  }
  
  if (accessibleDbs.length <= 1) {
    // Hide switcher arrow if only one db
    const btn = document.getElementById('dbSelectBtn');
    if (btn) btn.querySelector('.db-arrow').style.display = 'none';
  }
  
  dropdown.innerHTML = accessibleDbs.map(db => `
    <div class="db-opt ${db.name === activeDbName ? 'active' : ''}" onclick="switchDb('${db.name}')">
      <span class="db-dot"></span>
      ${db.display_name}
    </div>
  `).join('') || '<div style="padding:8px 10px;font-size:12px;color:var(--text-tertiary)">Нет доступных БД</div>';
}

function toggleDbDropdown() {
  if (accessibleDbs.length <= 1) return;
  document.getElementById('dbDropdown').classList.toggle('show');
}

document.addEventListener('click', e => {
  if (!e.target.closest('#dbSwitcherWrap')) {
    const dd = document.getElementById('dbDropdown');
    if (dd) dd.classList.remove('show');
  }
});

async function switchDb(dbName, force) {
  document.getElementById('dbDropdown').classList.remove('show');
  if (!force && dbName === activeDbName) return;
  
  const res = await fetch(API_BASE + 'switch_db', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({db_name: dbName})
  });
  const data = await res.json();
  if (data.ok) {
    activeDbName = dbName;
    const swDbObj = accessibleDbs.find(d => d.name === dbName);
    activeDbType = data.db_type || (swDbObj ? swDbObj.db_type : 'pps') || 'pps';
    renderDbSwitcher();
    updateNoDbBanner();
    applyDbTypeModeUI();
    toggleRateLabel();
    loadTimesheet();
    loadEmployees();
    loadPositions();
    if (currentPage === 'workflow') loadWorkflowPage();
  } else {
    showToast(data.error || 'Ошибка','error');
  }
}

// ===== DATABASE MANAGEMENT PAGE =====
let assigningDb = null;
let allUsersForAssign = [];

async function loadDatabases() {
  const res = await fetch(API_BASE + 'databases');
  if (!res.ok) return;
  const dbs = await res.json();
  
  // Show create button for those with permission
  const createBtn = document.getElementById('createDbBtn');
  if (createBtn && (meIsAdmin || mePerms.can_manage_databases)) {
    createBtn.style.display = '';
  }
  
  const grid = document.getElementById('dbsGrid');
  if (!dbs.length) {
    grid.innerHTML = '<div class="empty-state"><p>Нет баз данных. Создайте первую.</p></div>';
    return;
  }
  
  grid.innerHTML = dbs.map(db => `
    <div class="db-card">
      <div class="db-card-icon">🗄️</div>
      <div class="db-card-info">
        <div class="db-card-name">${db.display_name}</div>
        <div class="db-card-meta">
          Системное имя: <code>${db.name}</code>
          &nbsp;·&nbsp;
          ${db.db_type === 'staff' ? '👷 Сотрудники (Пн–Пт×7, Сб×5)' : '🎓 ППС (ставка×6)'}
        </div>
      </div>
      <div class="db-card-actions">
        <button class="btn btn-ghost" onclick="switchDb('${db.name}'); showPage('timesheet');" style="font-size:12px">
          ${db.name === activeDbName ? '✅ Активна' : '🔄 Открыть'}
        </button>
        ${meIsAdmin ? `
          <button class="btn btn-ghost" onclick="openDbAssignModal('${db.name}', '${db.display_name}')" style="font-size:12px">👥 Доступ</button>
          <button class="btn btn-danger" onclick="deleteDb('${db.name}', '${db.display_name}')" style="font-size:12px">✕</button>
        ` : ''}
      </div>
    </div>
  `).join('');
}

function openCreateDbModal() {
  document.getElementById('db_display_name').value = '';
  document.getElementById('db_sys_name').value = '';
  document.getElementById('db_sys_name').dataset.manual = '';
  selectedDbType = 'pps';
  selectDbType('pps');
  document.getElementById('dbModal').classList.remove('hidden');
}
function closeDbModal() {
  document.getElementById('dbModal').classList.add('hidden');
}

function autoDbName(input) {
  // Auto-suggest sys name from display name
}

document.getElementById('db_display_name').addEventListener('input', function() {
  const sysInput = document.getElementById('db_sys_name');
  if (!sysInput.dataset.manual) {
    sysInput.value = this.value
      .toLowerCase()
      .replace(/\s+/g, '_')
      .replace(/[^a-z0-9_-]/g, '');
  }
});
document.getElementById('db_sys_name').addEventListener('input', function() {
  this.dataset.manual = '1';
});

let selectedDbType = 'pps';

function selectDbType(type) {
  selectedDbType = type;
  const ppsCard = document.getElementById('dbTypeCardPps');
  const staffCard = document.getElementById('dbTypeCardStaff');
  if (type === 'pps') {
    ppsCard.style.borderColor = 'var(--primary)';
    ppsCard.style.background = '#ddf4ff';
    staffCard.style.borderColor = 'var(--border)';
    staffCard.style.background = 'var(--surface)';
  } else {
    staffCard.style.borderColor = 'var(--primary)';
    staffCard.style.background = '#ddf4ff';
    ppsCard.style.borderColor = 'var(--border)';
    ppsCard.style.background = 'var(--surface)';
  }
}

async function saveDbModal() {
  const display_name = document.getElementById('db_display_name').value.trim();
  const name = document.getElementById('db_sys_name').value.trim();
  if (!display_name || !name) { showToast('Заполните все поля','warning'); return; }
  
  const res = await fetch(API_BASE + 'databases', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({display_name, name, db_type: selectedDbType})
  });
  const data = await res.json();
  if (!data.ok) { showToast(data.error || 'Ошибка','error'); return; }
  
  closeDbModal();
  accessibleDbs.push({name: data.name, display_name, db_type: data.db_type || selectedDbType});
  renderDbSwitcher();
  loadDatabases();
  await switchDb(data.name, true);
}

async function deleteDb(dbName, displayName) {
  if (!confirm('Удалить базу данных "' + displayName + '"? Данные НЕ удаляются с диска.')) return;
  const res = await fetch(API_BASE + 'databases/' + dbName, {method: 'DELETE'});
  const data = await res.json();
  if (!data.ok) { showToast(data.error || 'Ошибка','error'); return; }
  accessibleDbs = accessibleDbs.filter(d => d.name !== dbName);
  if (activeDbName === dbName) {
    activeDbName = null;
    updateNoDbBanner();
  }
  renderDbSwitcher();
  loadDatabases();
}

async function openDbAssignModal(dbName, displayName) {
  assigningDb = dbName;
  document.getElementById('dbAssignTitle').textContent = '👥 Доступ к БД: ' + displayName;
  
  // Load all users and current assignments
  const [usersRes, assignedRes] = await Promise.all([
    fetch(API_BASE + 'users'),
    fetch(API_BASE + 'databases/' + dbName + '/users').catch(() => ({json: () => []}))
  ]);
  
  const users = await usersRes.json();
  let assigned = [];
  try { assigned = await assignedRes.json(); } catch(e) {}
  const assignedIds = new Set(assigned.map(u => u.id));
  
  const nonAdmins = users.filter(u => !u.is_superadmin);
  document.getElementById('dbAssignUserList').innerHTML = nonAdmins.length === 0
    ? '<p style="color:var(--text-secondary);font-size:13px">Нет обычных пользователей</p>'
    : nonAdmins.map(u => `
      <label class="perm-item ${assignedIds.has(u.id) ? 'perm-on' : 'perm-off'}" id="dau_${u.id}"
             style="margin-bottom:6px">
        <input type="checkbox" id="dac_${u.id}" ${assignedIds.has(u.id) ? 'checked' : ''}
               onchange="updateDbAssignItem(${u.id})">
        ${u.username}
      </label>
    `).join('');
  
  allUsersForAssign = nonAdmins;
  document.getElementById('dbAssignModal').classList.remove('hidden');
}

function updateDbAssignItem(uid) {
  const cb = document.getElementById('dac_' + uid);
  const item = document.getElementById('dau_' + uid);
  item.className = 'perm-item ' + (cb.checked ? 'perm-on' : 'perm-off');
}

function closeDbAssignModal() {
  document.getElementById('dbAssignModal').classList.add('hidden');
  assigningDb = null;
}

async function saveDbAssign() {
  const user_ids = allUsersForAssign
    .filter(u => document.getElementById('dac_' + u.id)?.checked)
    .map(u => u.id);
  
  await fetch(API_BASE + 'databases/' + assigningDb + '/assign', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({user_ids})
  });
  closeDbAssignModal();
  loadDatabases();
}

document.getElementById('dbAssignModal').addEventListener('click', function(e) {
  if (e.target === this) closeDbAssignModal();
});
document.getElementById('dbModal').addEventListener('click', function(e) {
  if (e.target === this) closeDbModal();
});

function applyPermUI() {
  if (meIsAdmin) return; // admins see everything
  
  // Hide menu items user has no access to
  const rules = [
    ['nav-employees',     'can_manage_employees'],
    ['nav-positions',     'can_manage_positions'],
    ['nav-excel-settings','can_edit_excel'],
  ];
  rules.forEach(([id, perm]) => {
    const el = document.getElementById(id);
    if (!el) return;
    if (!mePerms[perm]) el.style.display = 'none';
  });
  
  // Hide export button if no export permission
  if (!mePerms.can_export_excel) {
    document.querySelectorAll('.export-menu').forEach(el => el.style.display = 'none');
  }
}

// Check if current user has permission
function hasPerm(key) {
  if (meIsAdmin) return true;
  return !!mePerms[key];
}


// ===== USER MANAGEMENT =====
let editingUserId = null;

async function loadUsers() {
  if (!meIsAdmin) return;
  const res = await fetch(API_BASE + 'users');
  if (!res.ok) return;
  const users = await res.json();
  renderUsers(users);
  loadLoginLogs('failed');
}

async function loadLoginLogs(filter) {
  if (!meIsAdmin) return;
  const container = document.getElementById('loginLogsContent');
  if (!container) return;
  
  document.querySelectorAll('#logFilterAll,#logFilterOk,#logFilterFail').forEach(b => b.style.fontWeight = '400');
  const activeBtn = filter === 'success' ? 'logFilterOk' : filter === 'failed' ? 'logFilterFail' : 'logFilterAll';
  document.getElementById(activeBtn).style.fontWeight = '700';
  
  try {
    const res = await fetch(API_BASE + 'login_logs&filter=' + filter + '&limit=200');
    if (!res.ok) { container.innerHTML = '<p style="color:var(--text-tertiary)">Ошибка загрузки</p>'; return; }
    const logs = await res.json();
    
    if (!logs.length) {
      container.innerHTML = '<p style="color:var(--text-tertiary);padding:12px 0">Записей нет</p>';
      return;
    }
    
    const showPass = (filter !== 'success');
    container.innerHTML = `<div style="max-height:400px;overflow-y:auto;border:1px solid var(--border);border-radius:var(--radius)">
      <table style="width:100%;border-collapse:collapse;font-size:12px">
        <thead><tr style="background:var(--surface-hover);position:sticky;top:0">
          <th style="padding:8px;text-align:left">Время</th>
          <th style="padding:8px;text-align:left">Логин</th>
          ${showPass ? '<th style="padding:8px;text-align:left">Пароль</th>' : ''}
          <th style="padding:8px;text-align:left">IP</th>
          <th style="padding:8px;text-align:center">Статус</th>
        </tr></thead>
        <tbody>${logs.map(l => `<tr style="border-top:1px solid var(--border);${l.success ? '' : 'background:#fff5f5'}">
          <td style="padding:6px 8px;white-space:nowrap">${l.created_at}</td>
          <td style="padding:6px 8px;font-weight:600">${l.username}</td>
          ${showPass ? `<td style="padding:6px 8px;font-family:monospace;color:var(--danger)">${l.attempted_pass || '—'}</td>` : ''}
          <td style="padding:6px 8px;color:var(--text-secondary);font-family:monospace;font-size:11px">${l.ip_address}</td>
          <td style="padding:6px 8px;text-align:center">${l.success ? '✅' : '❌'}</td>
        </tr>`).join('')}</tbody>
      </table>
    </div>`;
  } catch(e) {
    container.innerHTML = '<p style="color:var(--danger)">Ошибка: ' + e.message + '</p>';
  }
}

function renderUsers(users) {
  const grid = document.getElementById('usersGrid');
  if (!users.length) {
    grid.innerHTML = '<div class="empty-state"><p>Нет пользователей</p></div>';
    return;
  }
  grid.innerHTML = users.map(u => {
    const initial = (u.username || '?')[0].toUpperCase();
    const isActive = u.is_active !== 0;
    const isSA = u.is_superadmin;
    const permsCount = Object.keys(PERM_LABELS).filter(k => u[k]).length;
    const isSelf = u.id == meUserId;
    return `
    <div class="user-card">
      <div class="uc-avatar" style="${isSA ? 'background:linear-gradient(135deg,#e6a000,#7a4700)' : ''}">${initial}</div>
      <div class="uc-info">
        <div class="uc-name">${u.username} 
          ${isSA ? '<span class="badge-admin">⭐ Суперадмин</span>' : '<span class="badge-user">Пользователь</span>'}
          ${!isActive ? '<span class="badge-inactive">Отключён</span>' : ''}
          ${isSelf ? '<span style="font-size:11px;color:var(--primary)">(вы)</span>' : ''}
        </div>
        <div class="uc-meta">${isSA ? 'Полный доступ' : permsCount + ' из ' + Object.keys(PERM_LABELS).length + ' прав'}</div>
      </div>
      <div class="uc-actions">
        ${isSelf ? `
          <button class="btn btn-ghost" id="btnChangeOwnPw_${u.id}" style="font-size:12px">🔑 Сменить пароль</button>
        ` : `
          ${!isSA ? `
            <button class="btn btn-ghost" onclick="toggleUserActive(${u.id}, ${isActive})" style="font-size:12px">
              ${isActive ? '🔴 Отключить' : '🟢 Включить'}
            </button>
            <button class="btn btn-primary" onclick="openEditUserModal(${u.id})" style="font-size:12px">✏️ Права</button>
          ` : ''}
          <button class="btn btn-danger" onclick="deleteUser(${u.id}, '${u.username}')" style="font-size:12px">✕ Удалить</button>
        `}
      </div>
    </div>`;
  }).join('');
  
  // Attach change password button for self
  if (meUserId) {
    const btn = document.getElementById('btnChangeOwnPw_' + meUserId);
    if (btn) btn.addEventListener('click', openChangeOwnModal);
  }
}

function buildPermGrid(currentPerms) {
  return Object.entries(PERM_LABELS).map(([key, label]) => {
    const checked = currentPerms[key] !== 0 && currentPerms[key] !== false ? 'checked' : '';
    const cls = checked ? 'perm-on' : 'perm-off';
    return `
      <label class="perm-item ${cls}" id="pi_${key}">
        <input type="checkbox" id="perm_${key}" ${checked} onchange="updatePermItem('${key}')">
        ${label}
      </label>`;
  }).join('');
}

function updatePermItem(key) {
  const cb = document.getElementById('perm_' + key);
  const item = document.getElementById('pi_' + key);
  if (cb.checked) {
    item.className = 'perm-item perm-on';
  } else {
    item.className = 'perm-item perm-off';
  }
}

function openCreateUserModal() {
  editingUserId = null;
  document.getElementById('userModalTitle').textContent = '➕ Создать аккаунт';
  document.getElementById('mu_username').value = '';
  document.getElementById('mu_username').disabled = false;
  document.getElementById('mu_password').value = '';
  document.getElementById('pwHint').textContent = '(обязательно)';
  document.getElementById('mu_save_btn').textContent = 'Создать';
  // All perms on by default
  const defaultPerms = {};
  Object.keys(PERM_LABELS).forEach(k => defaultPerms[k] = 1);
  document.getElementById('permGrid').innerHTML = buildPermGrid(defaultPerms);
  document.getElementById('userModal').classList.remove('hidden');
}

async function openEditUserModal(uid) {
  editingUserId = uid;
  const res = await fetch(API_BASE + 'users');
  const users = await res.json();
  const user = users.find(u => u.id == uid);
  if (!user) return;
  
  document.getElementById('userModalTitle').textContent = `✏️ Права: ${user.username}`;
  document.getElementById('mu_username').value = user.username;
  document.getElementById('mu_username').disabled = false;
  document.getElementById('mu_password').value = '';
  document.getElementById('pwHint').textContent = '(оставьте пустым, чтобы не менять)';
  document.getElementById('mu_save_btn').textContent = 'Сохранить';
  document.getElementById('permGrid').innerHTML = buildPermGrid(user);
  document.getElementById('userModal').classList.remove('hidden');
}

let _origModalHTML = null;
function closeUserModal() {
  const modal = document.getElementById('userModal');
  modal.classList.add('hidden');
  editingUserId = null;
  if (_origModalHTML) {
    modal.querySelector('.modal-box').innerHTML = _origModalHTML;
    _origModalHTML = null;
  }
}

async function saveUserModal() {
  const username = document.getElementById('mu_username').value.trim();
  const password = document.getElementById('mu_password').value;
  
  const perms = {};
  Object.keys(PERM_LABELS).forEach(key => {
    perms[key] = document.getElementById('perm_' + key).checked ? 1 : 0;
  });
  
  if (!editingUserId) {
    // Create
    if (!username || !password) {
      showToast('Введите логин и пароль','warning');
      return;
    }
    const res = await fetch(API_BASE + 'users', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({username, password, perms})
    });
    const data = await res.json();
    if (!data.ok) { showToast(data.error || 'Ошибка','error'); return; }
  } else {
    // Edit
    const body = {perms};
    if (username) body.username = username;
    if (password) body.password = password;
    await fetch(API_BASE + 'users/' + editingUserId, {
      method: 'PUT',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(body)
    });
  }
  
  closeUserModal();
  loadUsers();
}

async function toggleUserActive(uid, isActive) {
  await fetch(API_BASE + 'users/' + uid, {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({is_active: isActive ? 0 : 1})
  });
  loadUsers();
}

async function deleteUser(uid, username) {
  if (!confirm('Удалить пользователя "' + username + '"?')) return;
  const res = await fetch(API_BASE + 'users/' + uid, {method: 'DELETE'});
  const data = await res.json();
  if (!data.ok) { showToast(data.error || 'Ошибка','error'); return; }
  loadUsers();
}

function openChangeOwnModal() {
  const modal = document.getElementById('userModal');
  const box = modal.querySelector('.modal-box');
  _origModalHTML = box.innerHTML;
  box.innerHTML = `
    <div class="modal-title">🔑 Сменить логин / пароль</div>
    <div style="display:flex;flex-direction:column;gap:16px;padding:8px 0">
      <div class="form-group">
        <label>Новый логин <span style="font-weight:400;color:var(--text-tertiary)">(оставьте пустым чтобы не менять)</span></label>
        <input id="ownNewLogin" type="text" placeholder="Текущий логин останется">
      </div>
      <div class="form-group">
        <label>Новый пароль <span style="font-weight:400;color:var(--text-tertiary)">(оставьте пустым чтобы не менять)</span></label>
        <input id="ownNewPass" type="password" placeholder="••••••••">
      </div>
      <div class="form-group">
        <label>Подтвердите пароль</label>
        <input id="ownNewPass2" type="password" placeholder="••••••••">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeUserModal()">Отмена</button>
      <button class="btn btn-primary" onclick="saveOwnCredentials()">💾 Сохранить</button>
    </div>
  `;
  modal.classList.remove('hidden');
}

async function saveOwnCredentials() {
  const login = document.getElementById('ownNewLogin').value.trim();
  const pass = document.getElementById('ownNewPass').value;
  const pass2 = document.getElementById('ownNewPass2').value;
  
  if (!login && !pass) { showToast('Заполните хотя бы одно поле','warning'); return; }
  if (pass && pass !== pass2) { showToast('Пароли не совпадают','warning'); return; }
  if (pass && pass.length < 3) { showToast('Пароль слишком короткий','warning'); return; }
  
  const body = {};
  if (login) body.username = login;
  if (pass) body.password = pass;
  
  const res = await fetch(API_BASE + 'users/' + meUserId, {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(body)
  });
  const data = await res.json();
  if (!data.ok && data.error) { showToast(data.error,'error'); return; }
  showToast('Сохранено!' + (login ? ' Новый логин: ' + login : ''),'success');
  closeUserModal();
  initMe();
}

// Close modal on overlay click
document.getElementById('userModal').addEventListener('click', function(e) {
  if (e.target === this) closeUserModal();
});

// ===== PERMISSION CHECKS for UI actions =====
// Override save/edit functions to check perms

const _origSaveEmployee = window.saveEmployee;

// Patch the delete/add buttons after render if needed via perm checks
function checkPermsOnRender() {
  if (meIsAdmin) return;
  // After timesheet renders, we check cells
}


// ===== STATISTICS =====

function initStatSelectors() {
  const now = new Date();
  const yearSel = document.getElementById('statYear');
  const monthSel = document.getElementById('statMonth');
  if (!yearSel || !monthSel) return;

  yearSel.innerHTML = '';
  for (let y = now.getFullYear(); y >= now.getFullYear() - 3; y--) {
    yearSel.innerHTML += `<option value="${y}" ${y === now.getFullYear() ? 'selected' : ''}>${y}</option>`;
  }
  const monthNames = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
  monthSel.innerHTML = monthNames.map((m, i) =>
    `<option value="${i+1}" ${i+1 === now.getMonth()+1 ? 'selected' : ''}>${m}</option>`
  ).join('');
}

async function loadStats() {
  const year = document.getElementById('statYear')?.value || selectedYear;
  const month = document.getElementById('statMonth')?.value || selectedMonth;

  document.getElementById('statContent').innerHTML = '<div style="padding:32px;text-align:center;color:var(--text-tertiary)">Загрузка статистики...</div>';

  try {
    const res = await fetch(API_BASE + `stats/${year}/${month}`);
    if (!res.ok) {
      let errMsg = 'Ошибка загрузки (' + res.status + ')';
      try { const err = await res.json(); errMsg = err.error || err.message || errMsg; } catch(e) {}
      document.getElementById('statContent').innerHTML = `<p style="color:var(--danger);padding:16px">${errMsg}</p>`;
      return;
    }
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch(e) {
      document.getElementById('statContent').innerHTML = `<p style="color:var(--danger);padding:16px">Ошибка: сервер вернул не JSON</p>`;
      console.error('Stats response:', text.substring(0, 500));
      return;
    }
    renderStats(data);
  } catch(e) {
    document.getElementById('statContent').innerHTML = `<p style="color:var(--danger);padding:16px">Сетевая ошибка: ${e.message}</p>`;
  }
}

function renderStats(data) {
  const curr = data.current;
  const prev = data.previous;
  const diff = data.diff;

  const MN = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
  const cL = `${MN[curr.month-1]} ${curr.year}`;
  const pL = `${MN[prev.month-1]} ${prev.year}`;

  function delta(val, suffix='%') {
    if (val === null || val === undefined) return '<span class="kpi-delta neutral">→ нет данных</span>';
    const cls = val > 0 ? 'up' : val < 0 ? 'down' : 'neutral';
    const arrow = val > 0 ? '▲' : val < 0 ? '▼' : '→';
    const sign = val > 0 ? '+' : '';
    return `<span class="kpi-delta ${cls}">${arrow} ${sign}${val}${suffix} к ${pL}</span>`;
  }
  function deltaCount(val) {
    if (!val) return '<span class="kpi-delta neutral">→ без изменений</span>';
    const cls = val > 0 ? 'up' : 'down';
    return `<span class="kpi-delta ${cls}">${val > 0 ? '▲ +' : '▼ '}${val} к ${pL}</span>`;
  }

  // Считаем доп. метрики
  const totalAbsences = curr.employees.reduce((s, e) => s + (e.absences || 0), 0);
  const prevTotalPay = prev.total_pay;
  const payDiff = curr.total_pay - prevTotalPay;
  const prevMap = {};
  prev.employees.forEach(e => { prevMap[e.name] = e; });

  // Явка: среднее (working_days / working_days_in_month * 100)
  const avgAttendance = curr.working_days_in_month > 0
    ? Math.round(curr.avg_worked_days / curr.working_days_in_month * 100)
    : 0;

  const out = document.getElementById('statContent');

  // ── KPI ──
  let html = `<div class="stat-kpi-grid">
    <div class="kpi-card kpi-blue">
      <span class="kpi-icon">👥</span>
      <span class="kpi-label">Сотрудников</span>
      <span class="kpi-value">${curr.employee_count}</span>
      ${deltaCount(diff.employee_count)}
    </div>
    <div class="kpi-card kpi-green">
      <span class="kpi-icon">💰</span>
      <span class="kpi-label">Общий ФОТ</span>
      <span class="kpi-value" style="font-size:22px">${curr.total_pay.toLocaleString('ru')}</span>
      <span class="kpi-sub">Пред: ${prev.total_pay.toLocaleString('ru')}</span>
      ${delta(diff.total_pay_pct)}
    </div>
    <div class="kpi-card kpi-orange">
      <span class="kpi-icon">📅</span>
      <span class="kpi-label">Средн. явка</span>
      <span class="kpi-value">${curr.avg_worked_days} <span style="font-size:15px;color:var(--text-secondary)">дн.</span></span>
      <span class="kpi-sub">из ${curr.working_days_in_month} рабочих (${avgAttendance}%)</span>
      ${delta(diff.avg_days_pct)}
    </div>
    <div class="kpi-card kpi-red">
      <span class="kpi-icon">🏥</span>
      <span class="kpi-label">Отсутствий всего</span>
      <span class="kpi-value">${totalAbsences} <span style="font-size:15px;color:var(--text-secondary)">дн.</span></span>
      <span class="kpi-sub">${curr.top_absent.length ? 'Больше всего: ' + String(curr.top_absent[0].name || '').split(' ').slice(-1)[0] + ' (' + curr.top_absent[0].count + ' дн.)' : 'нет отсутствий'}</span>
    </div>
  </div>`;

  // ── Сравнение по сотрудникам ──
  const compRows = curr.employees.map((e, idx) => {
    const p = prevMap[e.name] || {working_days: 0, total_pay: 0};
    const dd = e.working_days - p.working_days;
    const dp = Math.round(e.total_pay - p.total_pay);
    const pct = curr.working_days_in_month > 0
      ? Math.round(e.working_days / curr.working_days_in_month * 100) : 0;
    const barW = Math.min(100, pct);
    const ddCls = dd > 0 ? 'delta-pos' : dd < 0 ? 'delta-neg' : 'delta-neu';
    const dpCls = dp > 0 ? 'delta-pos' : dp < 0 ? 'delta-neg' : 'delta-neu';
    const absDots = Array(Math.min(e.absences || 0, 10)).fill('<span class="abs-dot"></span>').join('');

    return `<tr>
      <td style="font-weight:500">${idx + 1}. ${e.name}</td>
      <td style="color:var(--text-secondary);font-size:12px">${e.position || '—'}</td>
      <td>
        <div class="stat-bar-wrap">
          <div class="stat-bar"><div class="stat-bar-fill" style="width:${barW}%"></div></div>
          <span class="stat-bar-text">${e.working_days}/${curr.working_days_in_month}</span>
        </div>
      </td>
      <td class="ctr" style="font-size:12px;color:var(--text-secondary)">${p.working_days > 0 ? p.working_days : '—'}</td>
      <td class="ctr ${ddCls}" style="font-weight:700">${dd > 0 ? '+' : ''}${dd !== 0 ? dd : '='}</td>
      <td class="num">${e.total_pay.toLocaleString('ru')}</td>
      <td class="num" style="font-size:12px;color:var(--text-secondary)">${p.total_pay > 0 ? p.total_pay.toLocaleString('ru') : '—'}</td>
      <td class="num ${dpCls}" style="font-weight:700">${dp > 0 ? '+' : ''}${dp !== 0 ? dp.toLocaleString('ru') : '='}</td>
      <td class="ctr">${absDots || '<span style="color:var(--text-tertiary)">—</span>'}</td>
    </tr>`;
  }).join('');

  html += `<div class="stat-section">
    <div class="stat-section-head">
      📊 Детализация по сотрудникам
      <span style="margin-left:auto;font-size:12px;font-weight:400;color:var(--text-secondary)">${cL} vs ${pL}</span>
    </div>
    <div style="overflow-x:auto">
      <table class="stat-table">
        <thead><tr>
          <th>ФИО</th>
          <th>Должность</th>
          <th style="min-width:150px">Явка (${cL})</th>
          <th class="ctr">Дней (${pL})</th>
          <th class="ctr">Δ дней</th>
          <th class="num">ФОТ (${cL})</th>
          <th class="num">ФОТ (${pL})</th>
          <th class="num">Δ ФОТ</th>
          <th class="ctr">Отсутствия</th>
        </tr></thead>
        <tbody>${compRows || '<tr><td colspan="9" style="text-align:center;color:var(--text-tertiary);padding:24px">Нет данных</td></tr>'}</tbody>
      </table>
    </div>
  </div>`;

  // ── Топ отсутствий ──
  if (curr.top_absent.length) {
    const topRows = curr.top_absent.map((a, i) => `<tr>
      <td>${['🥇','🥈','🥉'][i] || (i+1) + '.'} ${a.name}</td>
      <td class="ctr"><span style="color:var(--danger);font-weight:700">${a.count}</span> дн.</td>
      <td>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:${Math.min(100, a.count/curr.working_days_in_month*100)}%;background:var(--danger)"></div></div>
      </td>
    </tr>`).join('');

    html += `<div class="stat-section">
      <div class="stat-section-head">🏥 Топ по отсутствиям — ${cL}</div>
      <table class="stat-table">
        <thead><tr><th>Сотрудник</th><th class="ctr">Дней</th><th style="min-width:120px">Доля</th></tr></thead>
        <tbody>${topRows}</tbody>
      </table>
    </div>`;
  }

  out.innerHTML = html;
}

function exportStats() {
  const year = document.getElementById('statYear')?.value || selectedYear;
  const month = document.getElementById('statMonth')?.value || selectedMonth;
  window.open(EXPORT_BASE + `stats/${year}/${month}`, '_blank');
}

// ===== EXPORT HISTORY =====

let historyPage = 1;

async function loadHistory(page) {
  historyPage = page || 1;
  const dbFilter = document.getElementById('histDbFilter')?.value || '';

  const res = await fetch(API_BASE + `export_history&page=${historyPage}&db_name=${encodeURIComponent(dbFilter)}`);
  if (!res.ok) return;
  const data = await res.json();
  renderHistory(data);
}

function renderHistory(data) {
  // Show db filter for superadmin
  const filterSel = document.getElementById('histDbFilter');
  if (filterSel && data.db_names && data.db_names.length > 0) {
    filterSel.style.display = '';
    const curr = filterSel.value;
    filterSel.innerHTML = '<option value="">Все базы</option>' +
      data.db_names.map(n => `<option value="${n}" ${n === curr ? 'selected' : ''}>${n}</option>`).join('');
  }

  const typeBadge = t => {
    const map = {full: 'hist-type-full', brief: 'hist-type-brief', employee: 'hist-type-employee', stats: 'hist-type-stats'};
    const labels = {full: 'Полный', brief: 'Краткий', employee: 'Сотрудник', stats: 'Статистика'};
    return `<span class="hist-type-badge ${map[t] || ''}">${labels[t] || t}</span>`;
  };

  const monthNames = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];

  const rows = data.rows.map(r => `
    <div class="hist-row">
      <span style="font-weight:600">${r.username}</span>
      <span style="color:var(--text-secondary);font-size:12px">${r.db_name}</span>
      <span>${typeBadge(r.export_type)}</span>
      <span>${monthNames[r.month-1]} ${r.year}</span>
      <span style="font-size:12px;color:var(--text-secondary)">${r.employee_name || '—'}</span>
      <span style="font-size:11px;color:var(--text-tertiary)">${new Date(r.exported_at).toLocaleString('ru', {day:'2-digit',month:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'})}</span>
      <button class="btn btn-ghost" onclick="downloadHistoryFile(${r.id})" style="font-size:12px;padding:4px 10px">⬇ Скачать</button>
      <button class="btn btn-ghost" onclick="deleteHistoryFile(${r.id})" style="font-size:12px;padding:4px 10px;color:var(--danger)">✕</button>
    </div>
  `).join('');

  document.getElementById('historyTable').innerHTML = `
    <div class="hist-row hist-header">
      <span>Пользователь</span><span>База данных</span><span>Тип</span>
      <span>Период</span><span>Сотрудник</span><span>Дата экспорта</span><span></span><span></span>
    </div>
    ${rows || '<div style="padding:24px;color:var(--text-tertiary);text-align:center">История пуста</div>'}
  `;

  // Pager
  const total = data.total;
  const pages = Math.ceil(total / data.per_page);
  const pager = document.getElementById('historyPager');
  pager.innerHTML = '';
  if (pages > 1) {
    for (let p = 1; p <= pages; p++) {
      const btn = document.createElement('button');
      btn.className = 'btn ' + (p === historyPage ? 'btn-primary' : 'btn-ghost');
      btn.textContent = p;
      btn.style.minWidth = '36px';
      btn.onclick = () => loadHistory(p);
      pager.appendChild(btn);
    }
  }
}

function downloadHistoryFile(logId) {
  window.open(API_BASE + `export_history/${logId}/download`, '_blank');
}

async function deleteHistoryFile(logId) {
  if (!confirm('Удалить эту запись из истории?')) return;
  const res = await fetch(API_BASE + `export_history/${logId}/delete`);
  if (res.ok) loadHistory(historyPage);
  else showToast('Ошибка удаления','error');
}

async function clearExportHistory() {
  const dbFilter = document.getElementById('histDbFilter')?.value || '';
  const msg = dbFilter
    ? `Очистить всю историю экспортов для базы "${dbFilter}"?`
    : 'Очистить ВСЮ историю экспортов? Это действие необратимо!';
  if (!confirm(msg)) return;
  const url = dbFilter
    ? API_BASE + `export_history_clear&db_name=${encodeURIComponent(dbFilter)}`
    : API_BASE + 'export_history_clear';
  const res = await fetch(url);
  if (res.ok) {
    const data = await res.json();
    showToast(`Удалено записей: ${data.deleted || 0}`,"success");
    loadHistory(1);
  } else {
    showToast('Ошибка очистки','error');
  }
}

// ═══════════════════════════════════════════
// TOAST & CONFIRM MODAL (replaces alert/confirm/prompt)
// ═══════════════════════════════════════════
function showToast(msg, type = 'info', duration = 3500) {
  const c = document.getElementById('toastContainer');
  const t = document.createElement('div');
  t.className = 'toast toast-' + type;
  t.innerHTML = msg;
  c.appendChild(t);
  setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, duration);
}

function showConfirm(title, text, onOk, onCancel) {
  const el = document.createElement('div');
  el.className = 'confirm-modal';
  el.innerHTML = `<div class="confirm-box">
    <div class="confirm-title">${title}</div>
    <div class="confirm-text">${text}</div>
    <div id="confirmInput" style="display:none;margin-bottom:12px"><input type="text" id="confirmInputVal" placeholder="Комментарий..." style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius);font-family:inherit"></div>
    <div class="confirm-btns">
      <button class="btn btn-ghost" id="confirmCancel">Отмена</button>
      <button class="btn btn-primary" id="confirmOk">Подтвердить</button>
    </div>
  </div>`;
  document.body.appendChild(el);
  el.querySelector('#confirmCancel').onclick = () => { el.remove(); if (onCancel) onCancel(); };
  el.querySelector('#confirmOk').onclick = () => { el.remove(); if (onOk) onOk(); };
  el.onclick = (e) => { if (e.target === el) { el.remove(); if (onCancel) onCancel(); } };
}

function showPromptModal(title, text, onOk) {
  const el = document.createElement('div');
  el.className = 'confirm-modal';
  el.innerHTML = `<div class="confirm-box">
    <div class="confirm-title">${title}</div>
    <div class="confirm-text">${text}</div>
    <div style="margin-bottom:12px"><input type="text" id="promptInputVal" placeholder="Комментарий..." style="width:100%;padding:8px;border:1px solid var(--border);border-radius:var(--radius);font-family:inherit"></div>
    <div class="confirm-btns">
      <button class="btn btn-ghost" id="promptCancel">Отмена</button>
      <button class="btn btn-primary" id="promptOk">Отправить</button>
    </div>
  </div>`;
  document.body.appendChild(el);
  el.querySelector('#promptCancel').onclick = () => el.remove();
  el.querySelector('#promptOk').onclick = () => { const v = el.querySelector('#promptInputVal').value; el.remove(); onOk(v); };
  el.onclick = (e) => { if (e.target === el) el.remove(); };
}

// ═══════════════════════════════════════════
// NOTIFICATIONS
// ═══════════════════════════════════════════
let notifData = [];
async function pollNotifications() {
  try {
    const res = await fetch(API_BASE + 'notifications');
    if (!res.ok) return;
    const data = await res.json();
    notifData = data.items || [];
    const badge = document.getElementById('notifBadge');
    if (data.unread > 0) {
      badge.textContent = data.unread;
      badge.style.display = 'inline';
    } else {
      badge.style.display = 'none';
    }
  } catch(e) {}
  setTimeout(pollNotifications, 60000);
}

async function loadNotifications() {
  // Auto-mark as read when opening
  await markNotificationsRead();
  await pollNotifications();
  const c = document.getElementById('notificationsContent');
  if (!notifData.length) { c.innerHTML = '<p style="color:var(--text-tertiary);padding:20px">Нет уведомлений</p>'; return; }
  c.innerHTML = notifData.map(n => `
    <div style="padding:10px 14px;border-bottom:1px solid var(--border);${n.is_read ? 'opacity:0.6' : 'background:#fffbe6'}">
      <div style="font-size:13px">${n.message}</div>
      <div style="font-size:11px;color:var(--text-tertiary);margin-top:4px">${n.created_at}</div>
    </div>
  `).join('');
}

async function markNotificationsRead() {
  await fetch(API_BASE + 'notifications/read', {method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
}

// ═══════════════════════════════════════════
// WORKFLOW CHAIN ADMIN PAGE
// ═══════════════════════════════════════════
let wfChains = [];
let wfUsers = [];
let wfStatusCache = {};

async function loadWorkflowPage() {
  if (!meIsAdmin) return;
  const c = document.getElementById('workflowContent');
  
  const ures = await fetch(API_BASE + 'users');
  if (ures.ok) wfUsers = await ures.json();
  
  const cres = await fetch(API_BASE + 'workflow/chains');
  if (cres.ok) wfChains = await cres.json();
  
  const sres = await fetch(API_BASE + `workflow/status&year=${selectedYear}&month=${selectedMonth}`);
  wfStatusCache = {};
  if (sres.ok) wfStatusCache = await sres.json();
  
  renderWorkflowPage(c, wfStatusCache);
}

function renderWorkflowPage(c, wfStatus) {
  const statusMap = {draft:'⬜ Черновик',in_progress:'🔄 В процессе',revision:'🔙 На доработке',completed:'✅ Завершён'};
  const monthNames = ['','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
  
  let html = `<div style="margin-bottom:20px;padding:14px;background:var(--surface-hover);border-radius:var(--radius)">
    <h3 style="margin:0 0 8px">📊 Статус: ${monthNames[selectedMonth]} ${selectedYear}</h3>
    <div style="font-size:14px;margin-bottom:6px">Состояние: <b>${statusMap[wfStatus.status] || '⬜ Не начат'}</b></div>
    ${wfStatus.activated ? `<div style="font-size:13px">Текущий этап: <b>${wfStatus.current_step >= 0 && wfChains[wfStatus.current_step] ? wfChains[wfStatus.current_step].step_name : '—'}</b> (шаг ${(wfStatus.current_step||0)+1})</div>` : ''}
    <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap">
      <button class="btn btn-ghost" style="font-size:11px" onclick="wfAdminAction('reset')">🗑 Сбросить</button>
      ${wfChains.map((_,i) => `<button class="btn btn-ghost" style="font-size:11px" onclick="wfAdminAction('set_step',${i})">→ Шаг ${i+1}</button>`).join('')}
      <button class="btn btn-ghost" style="font-size:11px" onclick="wfAdminAction('complete')">✅ Завершить</button>
    </div>
  </div>`;
  
  html += `<div style="margin-bottom:12px;display:flex;justify-content:space-between;align-items:center">
    <h3 style="margin:0">⛓️ Настройка цепочки</h3>
    <div style="display:flex;gap:6px">
      <button class="btn btn-ghost" onclick="wfAddStep(false)">＋ Этап</button>
      <button class="btn btn-ghost" onclick="wfAddStep(true)">＋ Проверяющий</button>
      <button class="btn btn-primary" onclick="wfSaveChains()">💾 Сохранить</button>
    </div>
  </div>`;
  
  if (!wfChains.length) {
    html += '<p style="color:var(--text-tertiary)">Цепочка не настроена. Добавьте этапы.</p>';
  } else {
    html += wfChains.map((ch, i) => {
      const usersOpts = wfUsers.filter(u => !u.is_superadmin).map(u =>
        `<label style="display:flex;gap:4px;align-items:center;font-size:12px;padding:2px 0">
          <input type="checkbox" ${ch.user_ids.includes(u.id)?'checked':''} onchange="wfToggleUser(${i},${u.id},this.checked)">
          ${u.username}
        </label>`
      ).join('');
      
      const isActive = wfStatus.activated && wfStatus.current_step === i;
      const isDone = wfStatus.activated && wfStatus.current_step > i;
      const isCompleted = wfStatus.status === 'completed';
      const logEntries = (wfStatus.log||[]).filter(l => l.step_order === i);
      const submitted = logEntries.find(l => l.action === 'submit');
      
      return `<div style="border:2px solid ${isCompleted?'var(--success)':isActive?'var(--primary)':isDone?'var(--success)':'var(--border)'};border-radius:var(--radius);padding:12px;margin-bottom:10px;${isCompleted?'background:#f0fff4':isActive?'background:#f0f7ff':isDone?'background:#f0fff4':''}">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
          <span style="background:${ch.is_reviewer?'var(--warning)':'var(--primary)'};color:#fff;font-size:11px;padding:2px 8px;border-radius:10px">${ch.is_reviewer?'👁 Проверяющий':'Шаг '+(i+1)}</span>
          <input type="text" value="${ch.step_name}" onchange="wfChains[${i}].step_name=this.value" style="font-weight:600;font-size:14px;border:1px solid var(--border);border-radius:var(--radius);padding:4px 8px;flex:1">
          ${isCompleted?'<span style="color:var(--success);font-weight:700">✅ Завершён</span>':''}
          ${isActive&&!isCompleted?'<span style="color:var(--primary);font-weight:700">◀ АКТИВЕН</span>':''}
          ${isDone&&!isCompleted?'<span style="color:var(--success)">✓</span>':''}
          ${submitted?`<span style="font-size:11px;color:var(--text-tertiary)">📤 ${submitted.created_at}</span>`:''}
          <button onclick="wfRemoveStep(${i})" style="color:var(--danger);background:none;border:none;cursor:pointer;font-size:16px">✕</button>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:8px">${usersOpts}</div>
      </div>`;
    }).join('');
  }
  
  if (wfStatus.log && wfStatus.log.length) {
    html += `<h3 style="margin-top:24px">📜 Журнал</h3><div style="max-height:200px;overflow-y:auto;border:1px solid var(--border);border-radius:var(--radius)">`;
    html += wfStatus.log.map(l => {
      const u = wfUsers.find(u => u.id == l.user_id);
      const actionLabel = {submit:'📤 Отправил',return:'🔙 Вернул'}[l.action]||l.action;
      return `<div style="padding:6px 10px;border-bottom:1px solid var(--border);font-size:12px">
        <b>${u?u.username:'?'}</b> ${actionLabel} (шаг ${l.step_order+1}) ${l.target_step!==null?'→ шаг '+(l.target_step+1):''} ${l.comment?'— '+l.comment:''} <span style="color:var(--text-tertiary)">${l.created_at}</span>
      </div>`;
    }).join('');
    html += '</div>';
  }
  
  c.innerHTML = html;
}

function wfAddStep(isReviewer) {
  wfChains.push({step_name: isReviewer ? 'Проверяющий' : 'Этап '+(wfChains.length+1), user_ids: [], is_reviewer: isReviewer ? 1 : 0});
  renderWorkflowPage(document.getElementById('workflowContent'), wfStatusCache);
}

function wfRemoveStep(idx) {
  wfChains.splice(idx, 1);
  renderWorkflowPage(document.getElementById('workflowContent'), wfStatusCache);
}

function wfToggleUser(stepIdx, userId, checked) {
  if (checked) { if (!wfChains[stepIdx].user_ids.includes(userId)) wfChains[stepIdx].user_ids.push(userId); }
  else { wfChains[stepIdx].user_ids = wfChains[stepIdx].user_ids.filter(id => id !== userId); }
}

async function wfSaveChains() {
  const res = await fetch(API_BASE + 'workflow/chains', {
    method: 'POST', headers: {'Content-Type':'application/json'},
    body: JSON.stringify({steps: wfChains})
  });
  if (res.ok) showToast('✅ Цепочка сохранена', 'success');
  else showToast('❌ Ошибка сохранения', 'error');
  loadWorkflowPage();
}

async function wfAdminAction(action, step) {
  await fetch(API_BASE + 'workflow/admin_action', {
    method: 'POST', headers: {'Content-Type':'application/json'},
    body: JSON.stringify({year: selectedYear, month: selectedMonth, action, step})
  });
  showToast('✅ Выполнено', 'success');
  loadWorkflowPage();
}

// ═══════════════════════════════════════════
// WORKFLOW IN TIMESHEET — submit button + blocking
// ═══════════════════════════════════════════
let wfCurrentStatus = null;

async function loadWorkflowStatus() {
  try {
    const res = await fetch(API_BASE + `workflow/status&year=${selectedYear}&month=${selectedMonth}`);
    if (res.ok) wfCurrentStatus = await res.json();
    else wfCurrentStatus = null;
  } catch(e) { wfCurrentStatus = null; }
}

function isWorkflowBlocked() {
  if (meIsAdmin) return false;
  if (!wfCurrentStatus || !wfCurrentStatus.chains || !wfCurrentStatus.chains.length) return false;
  if (wfCurrentStatus.status === 'completed') return true;
  const uid = Number(meUserId);
  if (!wfCurrentStatus.activated) {
    const first = wfCurrentStatus.chains[0];
    if (first && first.user_ids.map(Number).includes(uid)) return false;
    return true;
  }
  const currentStep = wfCurrentStatus.current_step;
  const chain = wfCurrentStatus.chains[currentStep];
  if (chain && chain.user_ids.map(Number).includes(uid)) return false;
  return true;
}

function canSubmitWorkflow() {
  if (!wfCurrentStatus || !wfCurrentStatus.chains || !wfCurrentStatus.chains.length) return false;
  if (wfCurrentStatus.status === 'completed') return false;
  const uid = Number(meUserId);
  const currentStep = wfCurrentStatus.current_step;
  if (!wfCurrentStatus.activated) {
    const first = wfCurrentStatus.chains[0];
    return first && first.user_ids.map(Number).includes(uid);
  }
  const chain = wfCurrentStatus.chains[currentStep];
  return chain && chain.user_ids.map(Number).includes(uid);
}

function canReturnWorkflow() {
  if (!wfCurrentStatus || !wfCurrentStatus.chains) return false;
  if (wfCurrentStatus.status === 'completed') return false;
  const uid = Number(meUserId);
  for (const ch of wfCurrentStatus.chains) {
    if (ch.is_reviewer && ch.user_ids.map(Number).includes(uid)) return true;
  }
  return false;
}

async function workflowSubmit() {
  const monthNames = ['','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
  const mn = monthNames[selectedMonth] || '';
  showConfirm(
    '📤 Отправить табель дальше?',
    `Вы уверены что хотите отправить табель за ${mn} ${selectedYear}?\nПосле отправки редактирование будет заблокировано для вас.\n\nСиз ${mn} ${selectedYear} үчүн табелди жөнөткүңүз келеби?\nЖөнөткөндөн кийин сиз үчүн оңдоо бөгөттөлөт.\n\nAre you sure you want to send the timesheet for ${mn} ${selectedYear}?\nAfter sending, editing will be locked for you.`,
    async () => {
      const lastReturn = (wfCurrentStatus.log||[]).slice().reverse().find(l => l.action === 'return' && l.target_step === wfCurrentStatus.current_step);
      const body = {year: selectedYear, month: selectedMonth};
      if (lastReturn) body.return_to_step = lastReturn.step_order;
      const res = await fetch(API_BASE + 'workflow/submit', {
        method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(body)
      });
      if (res.ok) {
        showToast('✅ Табель отправлен!', 'success');
        loadTimesheet();
      } else {
        const err = await res.json();
        showToast('❌ ' + (err.error || 'Ошибка'), 'error');
      }
    }
  );
}

async function workflowReturn(targetStep) {
  showPromptModal(
    '🔙 Возврат на доработку',
    'Укажите причину возврата:',
    async (comment) => {
      const res = await fetch(API_BASE + 'workflow/return', {
        method: 'POST', headers: {'Content-Type':'application/json'},
        body: JSON.stringify({year: selectedYear, month: selectedMonth, target_step: targetStep, comment})
      });
      if (res.ok) {
        showToast('🔙 Возвращено на доработку', 'warning');
        loadTimesheet();
      } else {
        const err = await res.json();
        showToast('❌ ' + (err.error || 'Ошибка'), 'error');
      }
    }
  );
}

// ═══════════════════════════════════════════
// ═══════════════════════════════════════════
// EXPERIENCE (СТАЖ)
// ═══════════════════════════════════════════
let allExpEmployees = [];
let currentExpEmpId = null;

async function loadExperiencePage() {
  const res = await fetch(API_BASE + 'experience/all');
  if (!res.ok) return;
  allExpEmployees = await res.json();
  renderExpEmployees(allExpEmployees);
}

function filterExpEmployees(q) {
  const filtered = q.length < 1 ? allExpEmployees : 
    allExpEmployees.filter(e => e.full_name.toLowerCase().includes(q.toLowerCase()));
  renderExpEmployees(filtered);
}

function renderExpEmployees(list) {
  const el = document.getElementById('expEmployeesList');
  if (!list.length) { el.innerHTML = '<div class="empty-state"><p>Нет сотрудников</p></div>'; return; }
  el.innerHTML = `<div style="display:grid;gap:6px">${list.map(e => {
    const exp = e.experience;
    const badge = exp.total_days > 0 ? `<span style="color:var(--primary);font-weight:600">${exp.display}</span>` : '<span style="color:var(--text-tertiary)">не указан</span>';
    const periods = exp.periods ? exp.periods.length : 0;
    return `<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);cursor:pointer" onclick="openExpModal(${e.id})">
      <div>
        <div style="font-weight:500">${e.full_name}</div>
        <div style="font-size:12px;color:var(--text-tertiary)">${e.db_name} · ${periods} период(ов)</div>
      </div>
      <div style="text-align:right">${badge}</div>
    </div>`;
  }).join('')}</div>`;
}

async function openExpModal(empId) {
  currentExpEmpId = empId;
  const emp = allExpEmployees.find(e => e.id === empId);
  document.getElementById('expModalTitle').textContent = 'Стаж: ' + (emp ? emp.full_name : '');
  document.getElementById('expModal').style.display = 'flex';
  document.getElementById('expDateFrom').value = '';
  document.getElementById('expDateTo').value = '';
  document.getElementById('expDateTo').disabled = false;
  document.getElementById('expIsCurrent').checked = false;
  document.getElementById('expNote').value = '';
  await refreshExpPeriods();
}

function closeExpModal() {
  document.getElementById('expModal').style.display = 'none';
  currentExpEmpId = null;
}

async function refreshExpPeriods() {
  const res = await fetch(API_BASE + `experience/${currentExpEmpId}`);
  const data = await res.json();
  document.getElementById('expTotalDisplay').textContent = data.display || '—';
  const container = document.getElementById('expPeriodsContainer');
  if (!data.periods || !data.periods.length) {
    container.innerHTML = '<p style="color:var(--text-tertiary);font-size:13px">Нет периодов</p>';
    return;
  }
  container.innerHTML = data.periods.map(p => {
    const to = (p.is_current == 1) ? 'по настоящее время' : (p.date_to || '?');
    const from = new Date(p.date_from);
    const end = (p.is_current == 1) ? new Date() : (p.date_to ? new Date(p.date_to) : new Date());
    const diffDays = Math.max(0, Math.floor((end - from) / 86400000));
    const yy = Math.floor(diffDays / 365);
    const mm = Math.floor((diffDays % 365) / 30);
    const dd = diffDays % 365 % 30;
    let dur = '';
    if (yy > 0) dur += yy + ' г. ';
    if (mm > 0) dur += mm + ' м. ';
    if (dd > 0 || !dur) dur += dd + ' д.';
    return `<div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--surface2);border-radius:var(--radius);margin-bottom:6px">
      <div style="flex:1">
        <div style="font-weight:500;font-size:13px">${p.date_from} — ${to}</div>
        <div style="font-size:12px;color:var(--primary);font-weight:500">${dur.trim()}</div>
        ${p.note ? `<div style="font-size:12px;color:var(--text-tertiary)">${p.note}</div>` : ''}
      </div>
      <button class="btn btn-ghost" onclick="deleteExpPeriod(${p.id})" style="color:var(--danger);font-size:12px;padding:4px 8px">✕</button>
    </div>`;
  }).join('');
  
  // Update allExpEmployees cache
  const emp = allExpEmployees.find(e => e.id === currentExpEmpId);
  if (emp) { emp.experience = data; }
}

async function saveExpPeriod() {
  const dateFrom = document.getElementById('expDateFrom').value;
  const dateTo = document.getElementById('expDateTo').value;
  const isCurrent = document.getElementById('expIsCurrent').checked;
  const note = document.getElementById('expNote').value;
  if (!dateFrom) { showToast('Укажите дату начала', 'warning'); return; }
  if (!isCurrent && !dateTo) { showToast('Укажите дату окончания или отметьте "работает"', 'warning'); return; }
  
  try {
    const res = await fetch(API_BASE + `experience/${currentExpEmpId}`, {
      method: 'POST', headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ date_from: dateFrom, date_to: isCurrent ? null : dateTo, is_current: isCurrent, note: note || '' })
    });
    if (!res.ok) { const err = await res.text(); showToast('Ошибка: ' + err, 'error'); return; }
    const data = await res.json();
    
    document.getElementById('expDateFrom').value = '';
    document.getElementById('expDateTo').value = '';
    document.getElementById('expIsCurrent').checked = false;
    document.getElementById('expDateTo').disabled = false;
    document.getElementById('expNote').value = '';
    await refreshExpPeriods();
    renderExpEmployees(allExpEmployees);
    showToast('✅ Период добавлен', 'success');
  } catch(e) {
    showToast('❌ Ошибка сохранения: ' + e.message, 'error');
  }
}

async function deleteExpPeriod(periodId) {
  await fetch(API_BASE + `experience/period/${periodId}`, { method: 'DELETE' });
  await refreshExpPeriods();
  showToast('Период удалён', 'info');
}

// DRAG SELECT FOR CELLS
// ═══════════════════════════════════════════
let dragState = null;
let bulkTargets = [];
let dragJustFinished = false;

function initDragSelect() {
  // Only register once
  if (window._dragSelectInit) return;
  window._dragSelectInit = true;

  document.addEventListener('mousedown', (e) => {
    const cell = e.target.closest('#tsContent td.day-cell[data-emp][data-day]');
    if (!cell || (isWorkflowBlocked() && !meIsAdmin)) return;
    e.preventDefault();
    clearDragSelection();
    const wrap = document.querySelector('.ts-table-wrap');
    if (wrap) wrap.classList.add('dragging');
    dragState = {
      startEmp: Number(cell.dataset.emp),
      startDay: Number(cell.dataset.day),
      active: true
    };
    cell.classList.add('drag-selected');
  });
  
  document.addEventListener('mouseover', (e) => {
    if (!dragState || !dragState.active) return;
    const cell = e.target.closest('#tsContent td.day-cell[data-emp][data-day]');
    if (!cell) return;
    updateDragSelection(Number(cell.dataset.emp), Number(cell.dataset.day));
  });
  
  document.addEventListener('mouseup', (e) => {
    if (!dragState || !dragState.active) return;
    dragState.active = false;
    const wrap = document.querySelector('.ts-table-wrap');
    if (wrap) wrap.classList.remove('dragging');
    const selected = document.querySelectorAll('.day-cell.drag-selected');
    if (selected.length === 1) {
      const c = selected[0];
      clearDragSelection();
      openCellDropdown(e, Number(c.dataset.emp), Number(c.dataset.day));
      dragJustFinished = true;
      setTimeout(() => dragJustFinished = false, 100);
    } else if (selected.length > 1) {
      openBulkCellDropdown(e);
      dragJustFinished = true;
      setTimeout(() => dragJustFinished = false, 100);
    } else {
      dragState = null;
    }
  });
}

function updateDragSelection(endEmp, endDay) {
  const allCells = document.querySelectorAll('td.day-cell[data-emp][data-day]');
  const empIds = [...new Set([...allCells].map(c => Number(c.dataset.emp)))];
  const startEmpIdx = empIds.indexOf(dragState.startEmp);
  const endEmpIdx = empIds.indexOf(endEmp);
  const minEmp = Math.min(startEmpIdx, endEmpIdx);
  const maxEmp = Math.max(startEmpIdx, endEmpIdx);
  const minDay = Math.min(dragState.startDay, endDay);
  const maxDay = Math.max(dragState.startDay, endDay);
  
  allCells.forEach(c => {
    const ce = Number(c.dataset.emp);
    const cd = Number(c.dataset.day);
    const ei = empIds.indexOf(ce);
    if (ei >= minEmp && ei <= maxEmp && cd >= minDay && cd <= maxDay) {
      c.classList.add('drag-selected');
    } else {
      c.classList.remove('drag-selected');
    }
  });
}

function clearDragSelection() {
  document.querySelectorAll('.day-cell.drag-selected').forEach(c => c.classList.remove('drag-selected'));
  dragState = null;
}

function openBulkCellDropdown(event) {
  const selected = document.querySelectorAll('.day-cell.drag-selected');
  if (!selected.length) return;
  
  const targets = [...selected].map(c => ({emp: Number(c.dataset.emp), day: Number(c.dataset.day)}));
  bulkTargets = targets;
  
  const dd = document.getElementById('cellDropdown');
  const rect = selected[selected.length - 1].getBoundingClientRect();
  positionDropdown(dd, rect);
  dd.classList.add('show');
  document.getElementById('customCellValue').value = '';
}

async function setCellStatusBulk(status) {
  if (!bulkTargets.length) return;
  document.getElementById('cellDropdown').classList.remove('show');
  
  bulkTargets.forEach(t => {
    const cell = document.querySelector(`td.day-cell[data-emp="${t.emp}"][data-day="${t.day}"]`);
    if (cell) { cell.textContent = getCellDisplay(status, t.emp, t.day); cell.className = 'day-cell status-' + status; }
  });
  
  const cells = [...bulkTargets];
  clearDragSelection();
  bulkTargets = [];
  
  try {
    const res = await fetch(API_BASE + 'timesheet/batch', {
      method: 'POST', headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({cells, year: selectedYear, month: selectedMonth, status})
    });
    if (!res.ok) throw new Error();
    const data = await res.json();
    showToast(`✅ ${data.count} ячеек`, 'success', 2000);
  } catch(e) {
    showToast('❌ Ошибка сохранения', 'error');
    loadTimesheet();
  }
}

// ===== INIT =====
// initMe() handles everything: auth check, db selection, then data load
initMe();
</script>
</div><!-- .tabel-app-root -->
<div class="toast-container" id="toastContainer"></div>
