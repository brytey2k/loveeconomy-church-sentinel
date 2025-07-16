<script setup>
import {onMounted} from "vue";
import TopNavBar from "../Components/Structure/TopNavBar.vue";
import SideNavBar from "../Components/Structure/SideNavBar.vue";
import Footer from "../Components/Structure/Footer.vue";
import FlashMessages from "../Components/FlashMessages.vue";

onMounted(() => {
    document.body.classList.remove(['login-page']);
    document.body.classList.add(['sidebar-mini']);
})
</script>

<template>
    <div class="wrapper">
        <TopNavBar />

        <SideNavBar />

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <slot name="page-header"></slot>

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <slot name="page-title"></slot>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <slot name="breadcrumb"></slot>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <FlashMessages />

                <slot name="page-content"></slot>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <Footer />
    </div>
    <!-- ./wrapper -->
</template>
