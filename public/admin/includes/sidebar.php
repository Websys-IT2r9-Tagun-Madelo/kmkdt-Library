<?php $page = basename($_SERVER['PHP_SELF']); ?>

<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'index.php') ? '' : 'collapsed' ?> " href="index">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <li class="nav-heading">Library Management</li> 

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'Members.php') ? '' : 'collapsed' ?> " href="Members">
        <i class="bi bi-people"></i>
        <span>Members</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'Catalog.php') ? '' : 'collapsed' ?> " href="Catalog">
        <i class="bi bi-journal-bookmark"></i>
        <span>Catalog</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'Circulation.php') ? '' : 'collapsed' ?> " href="Circulation">
        <i class="bi bi-arrow-clockwise"></i>
        <span>Circulation</span>
      </a>
    </li>

    <li class="nav-heading">Client Management</li> 

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'Accounts.php') ? '' : 'collapsed' ?> " href="Accounts">
        <i class="bi bi-person-fill"></i>
        <span>Accounts</span>
      </a>
    </li>
    
    <li class="nav-item">
      <a class="nav-link <?= ($page == 'Contacts.php') ? '' : 'collapsed' ?> " href="Contacts">
        <i class="bi bi-envelope"></i>
        <span>Contacts</span>
      </a>
    </li>

    <li class="nav-heading">Analytics</li> 
     <!-- #region -->
        <li class="nav-item">
      <a class="nav-link <?= ($page == 'Reports.php') ? '' : 'collapsed' ?> " href="Reports">
        <i class="bi bi-flag"></i>
        <span>Reports</span>
      </a>
    </li>

  </ul>

</aside>

<main id="main" class="main">