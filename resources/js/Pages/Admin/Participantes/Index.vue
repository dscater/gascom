<script setup>
import Content from "@/Components/Content.vue";
import MiTable from "@/Components/MiTable.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useParticipantes } from "@/composables/participantes/useParticipantes";
import { useAxios } from "@/composables/axios/useAxios";
import { ref, onMounted, onBeforeMount } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
// import { useMenu } from "@/composables/useMenu";
import Formulario from "./Formulario.vue";
import { buttonProps } from "element-plus";
// const { mobile, identificaDispositivo } = useMenu();
const { props: props_page } = usePage();
const appStore = useAppStore();
onBeforeMount(() => {
    appStore.startLoading();
});

onMounted(() => {
    appStore.stopLoading();
});

const { setParticipante, limpiarParticipante, form } = useParticipantes();
const { axiosDelete, axiosPost } = useAxios();

const miTable = ref(null);
const headers = [
    {
        label: "NRO.",
        key: "id",
        sortable: true,
        width: "4%",
    },
    {
        label: "NOMBRE",
        key: "nombre",
        sortable: true,
    },
    {
        label: "CORREO",
        key: "correo",
        sortable: true,
    },
    {
        label: "DESCRIPCIÓN",
        key: "descripcion",
        sortable: true,
    },
    {
        label: "ACCIÓN",
        key: "accion",
        fixed: "right",
        width: "4%",
    },
];

const multiSearch = ref({
    search: "",
    filtro: [],
});

const muestra_formulario = ref(false);

const agregarRegistro = () => {
    limpiarParticipante();
    muestra_formulario.value = true;
};

const updateDatatable = async () => {
    if (miTable.value) {
        await miTable.value.cargarDatos();
        limpiarParticipante();
        muestra_formulario.value = false;
    }
};

const eliminarParticipante = (item) => {
    Swal.fire({
        title: "¿Quierés eliminar este registro?",
        html: `<strong>${item.full_name}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, eliminar",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "btn-danger",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            let respuesta = await axiosDelete(
                route("participantes.destroy", item.id),
            );
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};

const finalizarParticipante = (item) => {
    Swal.fire({
        title: "¿Quierés finalizar este Participante?",
        html: `<strong>${item.full_name}</strong>`,
        showCancelButton: true,
        confirmButtonText: "Si, finalizar",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "btn-danger",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            let respuesta = await axiosPost(
                route("participantes.finalizar", item.id),
                {
                    _method: "patch",
                },
            );
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};
</script>
<template>
    <Head title="Participantes"></Head>
    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-list"></i> Participantes
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">Participantes</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <button
                            v-if="
                                props_page.auth?.user.permisos == '*' ||
                                props_page.auth?.user.permisos.includes(
                                    'participantes.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm"
                            @click="agregarRegistro"
                        >
                            <i class="fa fa-plus"></i> Nuevo Participante
                        </button>
                    </div>
                    <div class="col-md-8 my-1">
                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input
                                        type="search"
                                        v-model="multiSearch.search"
                                        placeholder="Buscar"
                                        class="form-control border-1 border-right-0"
                                    />
                                    <div class="input-append">
                                        <button
                                            class="btn btn-default bg-white rounded-0"
                                            @click="updateDatos"
                                        >
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <MiTable
                            :tableClass="'bg-white mitabla'"
                            ref="miTable"
                            :cols="headers"
                            :api="true"
                            :url="route('participantes.paginado')"
                            :numPages="5"
                            :multiSearch="multiSearch"
                            :syncOrderBy="'id'"
                            :syncOrderAsc="'DESC'"
                            table-responsive
                            :header-class="'bg__primary'"
                            fixed-header
                        >
                            <template #ventas="{ item }">
                                <span
                                    class="badge text-xs"
                                    :class="[
                                        item.ventas == 1
                                            ? 'bgActivo'
                                            : 'bgPrecargado',
                                    ]"
                                    >{{
                                        item.ventas == 1 ? "VENTAS" : "ALMACÉN"
                                    }}</span
                                >
                            </template>
                            <template #activo="{ item }">
                                <span
                                    class="badge text-xs"
                                    :class="[
                                        item.activo == 1
                                            ? 'bgActivo'
                                            : 'bgInactivo',
                                    ]"
                                    >{{
                                        item.activo == 1 ? "ACTIVO" : "INACTIVO"
                                    }}</span
                                >
                            </template>
                            <template #accion="{ item }">
                                <template
                                    v-if="
                                        item.estado != 'FINALIZADO' &&
                                        (props_page.auth?.user.permisos ==
                                            '*' ||
                                            props_page.auth?.user.permisos.includes(
                                                'participantes.edit',
                                            ))
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Editar"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-warning"
                                            @click="
                                                setParticipante(item);
                                                muestra_formulario = true;
                                            "
                                        >
                                            <i class="fa fa-pen"></i></button
                                    ></el-tooltip>
                                </template>
                                <template
                                    v-if="
                                        item.estado == 'VIGENTE' &&
                                        (props_page.auth?.user.permisos ==
                                            '*' ||
                                            props_page.auth?.user.permisos.includes(
                                                'participantes.finalizar',
                                            ))
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Finalizar"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-primary"
                                            @click="finalizarParticipante(item)"
                                        >
                                            <i
                                                class="fa fa-flag-checkered"
                                            ></i></button
                                    ></el-tooltip>
                                </template>
                                <template
                                    v-if="
                                        item.estado != 'FINALIZADO' &&
                                        (props_page.auth?.user.permisos ==
                                            '*' ||
                                            props_page.auth?.user.permisos.includes(
                                                'participantes.destroy',
                                            ))
                                    "
                                >
                                    <el-tooltip
                                        class="box-item"
                                        effect="dark"
                                        content="Eliminar"
                                        placement="left-start"
                                    >
                                        <button
                                            class="btn btn-danger"
                                            @click="eliminarParticipante(item)"
                                        >
                                            <i
                                                class="fa fa-trash-alt"
                                            ></i></button
                                    ></el-tooltip>
                                </template>
                            </template>
                        </MiTable>
                    </div>
                </div>
            </div>
        </div>
        <Formulario
            v-if="muestra_formulario"
            :muestra_formulario="muestra_formulario"
            :form="form"
            @envio-formulario="updateDatatable"
            @cerrar-formulario="muestra_formulario = false"
        ></Formulario>
    </Content>
</template>
