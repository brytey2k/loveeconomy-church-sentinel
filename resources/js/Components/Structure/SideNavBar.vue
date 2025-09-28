<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const navGivingTypes = computed(() => page.props?.navGivingTypes ?? [])

// Auth related shared props
const userName = computed(() => page.props?.auth?.user?.name ?? '')
const permissions = computed(() => page.props?.auth?.permissions ?? [])

function can(permission) {
  if (!permission) return true
  return Array.isArray(permissions.value) && permissions.value.includes(permission)
}
</script>

<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <Link href="/dashboard" class="brand-link">
            <img :src="`/images/logo.png`" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">LEC</span>
        </Link>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img :src="`images/avatar.png`" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ userName || 'User' }}</a>
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
                    <li class="nav-item" v-if="can('view users') || can('view roles') || can('view permissions')">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Auth & Users
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item" v-if="can('view users')">
                                <Link href="/users" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Users</p>
                                </Link>
                            </li>
                            <li class="nav-item" v-if="can('view roles')">
                                <Link href="/roles" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Roles</p>
                                </Link>
                            </li>
                            <li class="nav-item" v-if="can('view permissions')">
                                <Link href="/permissions" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Permissions</p>
                                </Link>
                            </li>
                        </ul>
                    </li>
                    <!-- Organization Group -->
                    <li class="nav-item" v-if="can('view branches') || can('view countries') || can('view levels') || can('view positions')">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Organization
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item" v-if="can('view branches')">
                                <Link href="/branches" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Branches</p>
                                </Link>
                            </li>
                            <li class="nav-item" v-if="can('view countries')">
                                <Link href="/countries" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Countries</p>
                                </Link>
                            </li>
                            <li class="nav-item" v-if="can('view levels')">
                                <Link href="/levels" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Levels</p>
                                </Link>
                            </li>
                            <li class="nav-item" v-if="can('view positions')">
                                <Link href="/positions" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Positions</p>
                                </Link>
                            </li>
                        </ul>
                    </li>

                    <!-- Members & Giving Group -->
                    <li class="nav-item" v-if="can('view members') || can('manage giving types') || can('view transactions')">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-hand-holding-usd"></i>
                            <p>
                                Members & Giving
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item" v-if="can('view members')">
                                <Link href="/members" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Members</p>
                                </Link>
                            </li>
                            <!-- Dynamic Members by Giving Type inside group -->
                            <li class="nav-item" v-if="can('view members') && navGivingTypes.length">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        Members by Giving Type
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item" v-for="gt in navGivingTypes" :key="gt.id">
                                        <Link :href="`/members?giving_type_id=${gt.id}`" class="nav-link">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>{{ gt.name }}</p>
                                        </Link>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item" v-if="can('manage giving types')">
                                <Link href="/giving-types" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Giving Types</p>
                                </Link>
                            </li>
                            <li class="nav-item" v-if="can('view transactions')">
                                <Link href="/payments" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Payments</p>
                                </Link>
                            </li>
                        </ul>
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
