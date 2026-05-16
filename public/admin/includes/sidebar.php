<?php $page = basename($_SERVER['PHP_SELF']); ?>

<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'Index.php') ? '' : 'collapsed' ?> " href="Index">
        <i class="bi bi-grid"></i>
        <span class="ms-3">Dashboard</span>
      </a>
    </li>

    <?php $library_active = in_array($page, ['Members.php', 'Catalog.php', 'Circulation.php']); ?>
    <li class="nav-item">
      <a class="nav-link <?= $library_active ? '' : 'collapsed' ?>" data-bs-target="#library-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-bookmark-fill"></i><span class="ms-3">Library Management</span><i class="bi bi-chevron-down"></i>
      </a>
      <ul id="library-nav" class="nav-content collapse <?= $library_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="Members" class="<?= ($page == 'Members.php') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Members</span>
          </a>
        </li>
        <li>
          <a href="Catalog" class="<?= ($page == 'Catalog.php') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Catalog</span>
          </a>
        </li>
        <li>
          <a href="Circulation" class="<?= ($page == 'Circulation.php') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Circulation</span>
          </a>
        </li>
      </ul>
    </li>

    <?php $client_active = in_array($page, ['Accounts.php']); ?>
    <li class="nav-item">
      <a class="nav-link <?= $client_active ? '' : 'collapsed' ?>" data-bs-target="#client-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person-bounding-box"></i><span class="ms-3">Client Management</span><i class="bi bi-chevron-down"></i>
      </a>
      <ul id="client-nav" class="nav-content collapse <?= $client_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="Accounts" class="<?= ($page == 'Accounts.php') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Accounts</span>
          </a>
        </li>
      </ul>
    </li>

    <?php $analytics_active = in_array($page, ['PPH.php', 'Reports.php']); ?>
    <li class="nav-item">
      <a class="nav-link <?= $analytics_active ? '' : 'collapsed' ?>" data-bs-target="#analytics-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bar-chart-line-fill"></i><span class="ms-3">Analytics & Reporting</span><i class="bi bi-chevron-down"></i>
      </a>
      <ul id="analytics-nav" class="nav-content collapse <?= $analytics_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="PPH" class="<?= ($page == 'PPH.php') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Penalty Payment History</span>
          </a>
        </li>
        <li>
          <a href="Reports" class="<?= ($page == 'Reports.php') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Reports</span>
          </a>
        </li>
      </ul>
    </li>

    <?php $admin_active = in_array($page, ['Aprofile.php', 'Contacts.php']); ?>
    <li class="nav-item">
      <a class="nav-link <?= $admin_active ? '' : 'collapsed' ?>" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-person-vcard-fill"></i><span class="ms-3">Admin Hub</span><i class="bi bi-chevron-down"></i>
      </a>
      <ul id="admin-nav" class="nav-content collapse <?= $admin_active ? 'show' : '' ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a href="Aprofile" class="<?= ($page == 'Aprofile.php') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Profile</span>
          </a>
        </li>
        <li>
          <a href="Contacts" class="<?= ($page == 'Contacts.php') ? 'active' : '' ?>">
            <i class="bi bi-circle-fill"></i><span>Contacts</span>
          </a>
        </li>
      </ul>
    </li>

  </ul>

</aside>

<main id="main" class="main">