<?php 
// 1. Get the current script name/URI and strip any query strings
$current_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 2. Extract just the last part of the URL (the page identifier)
$page_raw = basename($current_uri); 

// 3. Strip out '.php' if it exists so we have a clean handle (e.g., 'payHistory')
$page = str_replace('.php', '', $page_raw); 
?>

<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'index') ? '' : 'collapsed' ?> " href="index">
        <i class="bi bi-grid"></i>
        <span class="ms-3">Dashboard</span>
      </a>
    </li>

    <?php $library_active = in_array($page, ['catalog', 'circulation']); ?>
    <li class="nav-item">
      <a class="nav-link <?= $library_active ? '' : 'collapsed' ?>" data-bs-target="#library-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-bookmark-fill"></i><span class="ms-3">Library Management</span><i class="bi bi-chevron-down"></i>
      </a>
      <ul id="library-nav" class="nav-content collapse <?= $library_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="catalog" class="<?= ($page == 'catalog') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Catalog</span>
          </a>
        </li>
        <li>
          <a href="circulation" class="<?= ($page == 'circulation') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Circulation</span>
          </a>
        </li>
      </ul>
    </li>

    <?php $client_active = in_array($page, ['members', 'messageHub']); ?>
    <li class="nav-item">
      <a class="nav-link <?= $client_active ? '' : 'collapsed' ?>" data-bs-target="#client-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person-bounding-box"></i><span class="ms-3">Member Management</span><i class="bi bi-chevron-down"></i>
      </a>
      <ul id="client-nav" class="nav-content collapse <?= $client_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="members" class="<?= ($page == 'members') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Members</span>
          </a>
        </li>
        <li>
          <a href="messageHub" class="<?= ($page == 'messageHub') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Message Hub</span>
          </a>
        </li>
      </ul>
    </li>

    <?php $analytics_active = in_array($page, ['payHistory', 'reports']); ?>
    <li class="nav-item">
      <a class="nav-link <?= $analytics_active ? '' : 'collapsed' ?>" data-bs-target="#analytics-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bar-chart-line-fill"></i><span class="ms-3">Analytics &amp; Reporting</span><i class="bi bi-chevron-down"></i>
      </a>
      <ul id="analytics-nav" class="nav-content collapse <?= $analytics_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="payHistory" class="<?= ($page == 'payHistory') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span> Payment History & Approval</span>
          </a>
        </li>
        <li>
          <a href="reports" class="<?= ($page == 'reports') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Reports</span>
          </a>
        </li>
      </ul>
    </li>

    <?php $admin_active = in_array($page, ['aProfile']); ?>
    <li class="nav-item">
      <a class="nav-link <?= $admin_active ? '' : 'collapsed' ?>" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person-vcard-fill"></i><span class="ms-3">Admin Info</span><i class="bi bi-chevron-down"></i>
      </a>
      <ul id="admin-nav" class="nav-content collapse <?= $admin_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="aProfile" class="<?= ($page == 'aProfile') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Profile</span>
          </a>
        </li>
      </ul>
    </li>

  </ul>

</aside>

<main id="main" class="main">