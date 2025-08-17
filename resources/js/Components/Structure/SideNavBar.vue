<script setup>
import {Link} from '@inertiajs/vue3';
</script>

<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <Link href="/dashboard" class="brand-link">
            <img :src="`/images/logo.png`" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">AdminLTE 3</span>
        </Link>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img :src="`images/avatar.png`" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">Alexander Pierce</a>
                </div>
            </div>

            <!-- SidebarSearch Form -->
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Starter Pages
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link active">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Active Page</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Inactive Page</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Simple Link
                                <span class="right badge badge-danger">New</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <Link href="/users" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Users</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/roles" class="nav-link">
                            <i class="nav-icon fas fa-user-shield"></i>
                            <p>Roles</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/permissions" class="nav-link">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Permissions</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/levels" class="nav-link">
                            <i class="nav-icon fas fa-signal"></i>
                            <p>Levels</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/branches" class="nav-link">
                            <i class="nav-icon fas fa-code-branch"></i>
                            <p>Branches</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/countries" class="nav-link">
                            <i class="nav-icon fas fa-flag"></i>
                            <p>Countries</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/members" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Members</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/positions" class="nav-link">
                            <i class="nav-icon fas fa-briefcase"></i>
                            <p>Positions</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/giving-types" class="nav-link">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Giving Types</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link href="/giving-type-systems" class="nav-link">
                            <i class="nav-icon fas fa-sitemap"></i>
                            <p>Giving Type Systems</p>
                        </Link>
                    </li>
                    <li class="nav-item">
                        <Link method="post" href="/logout" class="nav-link" as="button">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </Link>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>
