<script setup>
import Content from "@/Components/Content.vue";
import MiTable from "@/Components/MiTable.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useParticipanteGastos } from "@/composables/participante_gastos/useParticipanteGastos";
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

const { setParticipanteGasto, limpiarParticipanteGasto, form } =
    useParticipanteGastos();
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
        label: "PARTICIPANTE",
        key: "participante.nombre",
        sortable: true,
    },
    {
        label: "GASTO",
        key: "gasto.nombre",
        sortable: true,
    },
    {
        label: "PORCENTAJE %",
        key: "porcentaje",
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
    limpiarParticipanteGasto();
    muestra_formulario.value = true;
};

const updateDatatable = async () => {
    if (miTable.value) {
        await miTable.value.cargarDatos();
        limpiarParticipanteGasto();
        muestra_formulario.value = false;
    }
};

const eliminarParticipanteGasto = (item) => {
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
                route("participante_gastos.destroy", item.id),
            );
            if (respuesta && respuesta.sw) {
                updateDatatable();
            }
        }
    });
};

const finalizarParticipanteGasto = (item) => {
    Swal.fire({
        title: "¿Quierés finalizar este ParticipanteGasto?",
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
                route("participante_gastos.finalizar", item.id),
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
    <Head title="ParticipanteGastos"></Head>
    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-list"></i> ParticipanteGastos
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            ParticipanteGastos
                        </li>
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
                                    'participante_gastos.create',
                                )
                            "
                            type="button"
                            class="btn btn-primary text-sm"
                            @click="agregarRegistro"
                        >
                            <i class="fa fa-plus"></i> Nuevo Participante Gasto
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
                            :url="route('participante_gastos.paginado')"
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
                                                'participante_gastos.edit',
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
                                                setParticipanteGasto(item);
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
                                                'participante_gastos.finalizar',
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
                                            @click="
                                                finalizarParticipanteGasto(item)
                                            "
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
                                                'participante_gastos.destroy',
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
                                            @click="
                                                eliminarParticipanteGasto(item)
                                            "
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
