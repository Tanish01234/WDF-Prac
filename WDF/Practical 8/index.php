<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Practical 08: Relational ER Model & Schema Architecture</title>
  <link rel="stylesheet" href="../../css/global.css">
  <link rel="stylesheet" href="../../css/components.css">
  <style>
    body { background: var(--bg-primary); color: var(--text-primary); padding: 2.5rem 1.5rem; }
    .container { max-width: 1040px; margin: 0 auto; }
    .card { background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-lg); padding: 2rem; margin-bottom: 2rem; box-shadow: var(--shadow-md); }
    .entity-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem; }
    .entity-box { background: var(--bg-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 1.25rem; }
    .entity-title { font-size: 1.1rem; font-weight: 700; color: var(--accent-primary); border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem; margin-bottom: 0.75rem; display: flex; justify-content: space-between; }
    .field-row { display: flex; justify-content: space-between; font-size: 0.8rem; padding: 0.25rem 0; font-family: monospace; }
    .pk { color: #f59e0b; font-weight: 700; }
    .fk { color: #3b82f6; font-weight: 700; }
  </style>
</head>
<body>
  <div class="container">
    <header style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-medium); padding-bottom: 1rem;">
      <span class="badge badge-accent">Practical 08 Demonstration</span>
      <h1 style="font-size: 2rem; font-weight: 800; margin-top: 0.35rem;">Normalized MySQL Schema & Entity-Relationship Architecture</h1>
      <p style="color: var(--text-secondary); font-size: 0.95rem;">
        Third Normal Form (3NF) relational design for the Synora Academic Student Portal with dual PDO & MySQLi drivers.
      </p>
      <div style="margin-top: 1rem;">
        <a href="connection-test.php" class="btn btn-primary btn-sm">⚡ Run Live Connectivity Test</a>
      </div>
    </header>

    <!-- Schema Entities -->
    <section class="card">
      <h2 style="font-size: 1.35rem; margin-bottom: 0.5rem;">Database Entities & Attribute Dictionary</h2>
      <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 1.25rem;">
        The relational schema <code>synora</code> enforces strict referential integrity with cascading updates and deletions.
      </p>

      <div class="entity-grid">
        <!-- 1. users -->
        <div class="entity-box">
          <div class="entity-title"><span>users</span> <span class="badge badge-blue">Auth</span></div>
          <div class="field-row"><span class="pk">id (PK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span>username (UQ)</span> <span>VARCHAR(50)</span></div>
          <div class="field-row"><span>email (UQ)</span> <span>VARCHAR(120)</span></div>
          <div class="field-row"><span>password_hash</span> <span>VARCHAR(255)</span></div>
          <div class="field-row"><span>role</span> <span>ENUM('student','admin')</span></div>
          <div class="field-row"><span>status</span> <span>ENUM('active','inactive')</span></div>
        </div>

        <!-- 2. students -->
        <div class="entity-box">
          <div class="entity-title"><span>students</span> <span class="badge badge-green">Directory</span></div>
          <div class="field-row"><span class="pk">id (PK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span class="fk">user_id (FK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span>roll_no (UQ)</span> <span>VARCHAR(20)</span></div>
          <div class="field-row"><span>name</span> <span>VARCHAR(100)</span></div>
          <div class="field-row"><span>email (UQ)</span> <span>VARCHAR(120)</span></div>
          <div class="field-row"><span>mobile</span> <span>VARCHAR(15)</span></div>
          <div class="field-row"><span>batch / div</span> <span>VARCHAR(10)</span></div>
        </div>

        <!-- 3. events -->
        <div class="entity-box">
          <div class="entity-title"><span>events</span> <span class="badge badge-accent">Workshops</span></div>
          <div class="field-row"><span class="pk">id (PK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span>title</span> <span>VARCHAR(150)</span></div>
          <div class="field-row"><span>category</span> <span>VARCHAR(50)</span></div>
          <div class="field-row"><span>event_date</span> <span>DATE</span></div>
          <div class="field-row"><span>capacity</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span>poster_url</span> <span>VARCHAR(255)</span></div>
          <div class="field-row"><span>status</span> <span>ENUM('Open','Closed')</span></div>
        </div>

        <!-- 4. registrations -->
        <div class="entity-box">
          <div class="entity-title"><span>registrations</span> <span class="badge badge-blue">M:N Associative</span></div>
          <div class="field-row"><span class="pk">id (PK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span class="fk">student_id (FK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span class="fk">event_id (FK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span>status</span> <span>ENUM('Registered','Waitlist')</span></div>
          <div class="field-row"><span>registered_at</span> <span>TIMESTAMP</span></div>
        </div>

        <!-- 5. feedback -->
        <div class="entity-box">
          <div class="entity-title"><span>feedback</span> <span class="badge badge-green">Survey</span></div>
          <div class="field-row"><span class="pk">id (PK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span>name</span> <span>VARCHAR(100)</span></div>
          <div class="field-row"><span>email</span> <span>VARCHAR(120)</span></div>
          <div class="field-row"><span>category</span> <span>VARCHAR(50)</span></div>
          <div class="field-row"><span>rating</span> <span>TINYINT UNSIGNED</span></div>
          <div class="field-row"><span>comments</span> <span>TEXT</span></div>
        </div>

        <!-- 6. audit_logs -->
        <div class="entity-box">
          <div class="entity-title"><span>audit_logs</span> <span class="badge badge-accent">Security</span></div>
          <div class="field-row"><span class="pk">id (PK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span class="fk">user_id (FK)</span> <span>INT UNSIGNED</span></div>
          <div class="field-row"><span>action</span> <span>VARCHAR(100)</span></div>
          <div class="field-row"><span>details</span> <span>TEXT</span></div>
          <div class="field-row"><span>ip_address</span> <span>VARCHAR(45)</span></div>
          <div class="field-row"><span>created_at</span> <span>TIMESTAMP</span></div>
        </div>
      </div>
    </section>

    <!-- Normalization Proof -->
    <section class="card">
      <h2 style="font-size: 1.35rem; margin-bottom: 0.75rem;">Third Normal Form (3NF) Normalization Proof</h2>
      <ul style="color: var(--text-secondary); line-height: 1.8; font-size: 0.9rem; padding-left: 1.25rem;">
        <li><strong>First Normal Form (1NF):</strong> All attributes contain atomic, scalar values (no multi-valued CSV columns). Each table has a defined primary key (`id`).</li>
        <li><strong>Second Normal Form (2NF):</strong> Achieves 1NF and contains no partial dependencies. In `registrations`, non-key attributes (`status`, `registered_at`) depend on the whole composite identity (`student_id`, `event_id`).</li>
        <li><strong>Third Normal Form (3NF):</strong> Achieves 2NF and contains no transitive dependencies ($X \to Y \to Z$). Student profiles and user credentials reside in distinct entities linked via foreign keys (`user_id`).</li>
      </ul>
    </section>
  </div>
</body>
</html>
