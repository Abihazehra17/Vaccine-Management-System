<?php
$pageTitle = 'List of Vaccine - VaxCare Hospital Portal';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
  <?php require_once __DIR__ . '/includes/navbar.php'; ?>

  <main class="p-3 p-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <div class="p-2 rounded-2" style="background: rgba(34, 197, 94, 0.15); color: #22c55e;">
            <i class="bi bi-eyedropper fs-5"></i>
          </div>
          <h4 class="fw-bold mb-0" style="color: var(--vax-text-main);">List of Vaccine & Availability</h4>
        </div>
        <div class="small" style="color: var(--vax-text-muted);">
          Dashboard / <span class="text-secondary">Hospital Vaccine Stock & Clinical Roster</span>
        </div>
      </div>
      <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" onclick="window.print()">
        <i class="bi bi-printer"></i>
        <span>Print Vaccine List</span>
      </button>
    </div>

    <!-- 4 Summary Stats (VaxCare Style) -->
    <div class="row g-3 mb-4 mt-1">
      <div class="col-6 col-md-3">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-purple">
            <i class="bi bi-capsule"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Catalog Vaccines</span>
            <span class="stat-number">6 Types</span>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-green">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">In Stock Doses</span>
            <span class="stat-number">1,420</span>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-blue">
            <i class="bi bi-thermometer-half"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Cold Storage</span>
            <span class="stat-number">+3.5 °C</span>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-red">
            <i class="bi bi-exclamation-triangle-fill"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Low / Out of Stock</span>
            <span class="stat-number">1</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Vaccine Inventory Table Card -->
    <div class="card-vax p-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark">EPI Immunization Vaccine Catalog</h6>
        <span class="badge bg-light text-secondary border">Storage: Main Pharmacy Cold Vault #1</span>
      </div>

      <div class="table-responsive">
        <table class="table-vax">
          <thead>
            <tr>
              <th>VACCINE NAME</th>
              <th>TARGET DISEASE</th>
              <th>ELIGIBLE AGE SCHEDULE</th>
              <th>CURRENT STOCK</th>
              <th>STATUS</th>
              <th class="text-end">AVAILABILITY TOGGLE</th>
            </tr>
          </thead>
          <tbody>
            <!-- Vaccine 1 -->
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="vaccine-badge-purple">OPV</span>
                  <div>
                    <div class="fw-bold text-dark">Oral Polio Vaccine (bOPV)</div>
                    <small class="text-muted">Batch: POL-2026-X8 • Exp: Dec 2027</small>
                  </div>
                </div>
              </td>
              <td>Poliomyelitis</td>
              <td><span class="badge bg-light text-dark border">At Birth, 6, 10, 14 Weeks</span></td>
              <td><strong class="text-dark">480 Doses</strong> <span class="text-muted small">(24 Vials)</span></td>
              <td>
                <span class="status-pill status-pill-vaccinated">
                  <i class="bi bi-check-circle-fill"></i> Available
                </span>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="alert('Status toggled: Marked Unavailable')">
                  <i class="bi bi-toggle-on me-1"></i> Mark Unavailable
                </button>
              </td>
            </tr>

            <!-- Vaccine 2 -->
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="vaccine-badge-purple">BCG</span>
                  <div>
                    <div class="fw-bold text-dark">BCG Vaccine</div>
                    <small class="text-muted">Batch: BCG-901-A • Exp: Aug 2027</small>
                  </div>
                </div>
              </td>
              <td>Tuberculosis</td>
              <td><span class="badge bg-light text-dark border">At Birth</span></td>
              <td><strong class="text-dark">320 Doses</strong> <span class="text-muted small">(16 Vials)</span></td>
              <td>
                <span class="status-pill status-pill-vaccinated">
                  <i class="bi bi-check-circle-fill"></i> Available
                </span>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="alert('Status toggled: Marked Unavailable')">
                  <i class="bi bi-toggle-on me-1"></i> Mark Unavailable
                </button>
              </td>
            </tr>

            <!-- Vaccine 3 -->
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="vaccine-badge-purple">HEPB</span>
                  <div>
                    <div class="fw-bold text-dark">Hepatitis B (Birth Dose)</div>
                    <small class="text-muted">Batch: HEPB-441 • Exp: Feb 2028</small>
                  </div>
                </div>
              </td>
              <td>Hepatitis B Infection</td>
              <td><span class="badge bg-light text-dark border">Within 24 Hours of Birth</span></td>
              <td><strong class="text-dark">250 Doses</strong> <span class="text-muted small">(25 Vials)</span></td>
              <td>
                <span class="status-pill status-pill-vaccinated">
                  <i class="bi bi-check-circle-fill"></i> Available
                </span>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="alert('Status toggled: Marked Unavailable')">
                  <i class="bi bi-toggle-on me-1"></i> Mark Unavailable
                </button>
              </td>
            </tr>

            <!-- Vaccine 4 -->
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="vaccine-badge-purple">PENTA</span>
                  <div>
                    <div class="fw-bold text-dark">Pentavalent (DTP-HepB-Hib)</div>
                    <small class="text-muted">Batch: PENTA-512 • Exp: Oct 2027</small>
                  </div>
                </div>
              </td>
              <td>Diphtheria, Tetanus, Pertussis, HepB, Hib</td>
              <td><span class="badge bg-light text-dark border">6, 10, 14 Weeks</span></td>
              <td><strong class="text-dark">310 Doses</strong> <span class="text-muted small">(31 Vials)</span></td>
              <td>
                <span class="status-pill status-pill-vaccinated">
                  <i class="bi bi-check-circle-fill"></i> Available
                </span>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="alert('Status toggled: Marked Unavailable')">
                  <i class="bi bi-toggle-on me-1"></i> Mark Unavailable
                </button>
              </td>
            </tr>

            <!-- Vaccine 5 -->
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="vaccine-badge-purple">MR</span>
                  <div>
                    <div class="fw-bold text-dark">Measles-Rubella (MR)</div>
                    <small class="text-muted">Batch: MR-104 • Exp: Nov 2026</small>
                  </div>
                </div>
              </td>
              <td>Measles & Rubella</td>
              <td><span class="badge bg-light text-dark border">9 Months & 15 Months</span></td>
              <td><strong class="text-warning">60 Doses</strong> <span class="text-danger small">(Low Stock)</span></td>
              <td>
                <span class="status-pill status-pill-vaccinated">
                  <i class="bi bi-check-circle-fill"></i> Available
                </span>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="alert('Status toggled: Marked Unavailable')">
                  <i class="bi bi-toggle-on me-1"></i> Mark Unavailable
                </button>
              </td>
            </tr>

            <!-- Vaccine 6 -->
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="vaccine-badge-purple" style="background: #64748b;">ROTA</span>
                  <div>
                    <div class="fw-bold text-muted">Rotavirus Vaccine (Rotavac)</div>
                    <small class="text-muted">Supply pending from Central Health Depot</small>
                  </div>
                </div>
              </td>
              <td>Rotaviral Diarrhea</td>
              <td><span class="badge bg-light text-muted border">6 & 10 Weeks</span></td>
              <td><strong class="text-danger">0 Doses</strong></td>
              <td>
                <span class="status-pill status-pill-missed">
                  <i class="bi bi-x-circle-fill"></i> Unavailable
                </span>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="alert('Status toggled: Marked Available')">
                  <i class="bi bi-toggle-off me-1"></i> Mark Available
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>
