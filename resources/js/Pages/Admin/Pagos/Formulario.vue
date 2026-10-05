<script setup>
// TOAST
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { router } from "@inertiajs/vue3";
import { watch, ref, computed, onMounted, nextTick } from "vue";
import MiPaginacion from "@/Components/MiPaginacion.vue";

const props = defineProps({
    form: {
        type: Object,
    },
});

const enviando = ref(false);
const form = props.form;

const textBtn = computed(() => {
    if (enviando.value) {
        return `<i class="fa fa-spin fa-spinner"></i> Enviando...`;
    }
    if (form.id == 0) {
        return `<i class="fa fa-save"></i> Registrar Pago`;
    }
    return `<i class="fa fa-edit"></i> Actualizar Pago`;
});

const txtBtnCancelar = computed(() => {
    if (form.id == 0) {
        return `<i class="fa fa-times"></i> Cancelar Pago`;
    }
    return `<i class="fa fa-times"></i> Cancelar Actualización`;
});

const cancelarPago = () => {
    const message =
        form.id == 0
            ? `Se cancelará el PAGO`
            : `Se cancelará la ACTUALIZACIÓN DEL PAGO ${form.id}`;

    Swal.fire({
        // icon: "question",
        title: "¿Cancelar regitro?",
        html: `${message}`,
        showCancelButton: true,
        confirmButtonText: "Si, Cancelar",
        cancelButtonText: "No, cancelar",
        denyButtonText: `No, cancelar`,
        customClass: {
            confirmButton: "btn-danger",
        },
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            if (form.id != 0) {
                router.get(route("pagos.index"));
            } else {
                form.reset();
                cargarProductos();
            }
        }
    });
};

const participante_id = ref(null);
const enviarFormulario = () => {
    enviando.value = true;
    form.participante_id = participante_id.value;
    let url =
        form.id == 0 ? route("pagos.store") : route("pagos.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (response) => {
            console.log("correcto");
            const success =
                response.props.flash.success ?? "Proceso realizado con éxito";
            const url_blank = response.props.url_blank ?? null;
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                confirmButtonText: `Aceptar`,
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });

            if (url_blank) {
                window.open(url_blank, "_blank");
            }

            cargarProductos();
            emits("envio-formulario");
        },
        onError: (err, code) => {
            console.log(code ?? "");
            if (Object.keys(form.errors).length > 0) {
                const errores = Object.values(form.errors)
                    .map((error) => `<li>${error}</li>`)
                    .join("");

                Swal.fire({
                    icon: "error",
                    title: "Errores de validación",
                    html: `
                <p>Existen errores en el formulario:</p>
                <ul style="text-align:left;">
                    ${errores}
                </ul>
            `,
                    confirmButtonText: "Aceptar",
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Ocurrió un error inesperado, contacte con el administrador.",
                    confirmButtonText: "Aceptar",
                    customClass: {
                        confirmButton: "btn-error",
                    },
                });
            }
        },
        onFinish: () => {
            enviando.value = false;
        },
    });
};

const emits = defineEmits(["envio-formulario"]);

const listParticipantes = ref([]);
const listGastos = ref([]);

const cargarParticipantes = async () => {
    try {
        const res = await axios.get(route("participantes.listado"));
        listParticipantes.value = res.data.participantes;
    } catch (e) {
        console.log(e);
    } finally {
    }
};
const cargarGastos = async () => {
    try {
        const res = await axios.get(route("gastos.listado"));
        listGastos.value = res.data.gastos;
    } catch (e) {
        console.log(e);
    } finally {
    }
};

const cargarListas = () => {
    cargarGastos();
    cargarParticipantes();
};

onMounted(() => {
    cargarListas();
});
</script>

<template>
    <form @submit.prevent="enviarFormulario()">
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header shadow-bottom">
                        <div class="row">
                            <div class="col-12 mt-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa fa-warehouse"></i>
                                    </span>
                                    <div class="form-control border-0 p-0">
                                        <el-select
                                            v-model="participante_id"
                                            class="el-select-input-group-right"
                                            no-data-text="Sin datos"
                                            no-match-text="Sin resultados"
                                            placeholder="Seleccionar Almacén"
                                            filterable
                                        >
                                            <el-option
                                                v-for="item in listParticipantes"
                                                :key="item.id"
                                                :value="item.id"
                                                :label="`${item.nombre}`"
                                            ></el-option>
                                        </el-select>
                                    </div>
                                </div>
                                <ul
                                    v-if="form.errors?.participante_id"
                                    class="d-block text-danger list-unstyled"
                                >
                                    <li class="parsley-required">
                                        {{ form.errors?.participante_id }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="col-12">
                            <h4 class="card-title text-center w-100">
                                <i class="fa fa-truck-loading"></i> Agregar
                                Productos
                            </h4>
                        </div>
                    </div>
                    <div
                        class="card-body bgGrayLight"
                        style="max-height: 63vh; overflow: auto"
                    >
                        <div class="row" v-if="participante_id">
                            <div class="col-12">
                                <div class="vacio_info" v-if="loadingLista">
                                    <i
                                        class="fa fa-spin fa-spinner fs-1 text-primary"
                                    ></i>
                                </div>
                                <div class="row" v-if="!loadingLista"></div>
                            </div>
                        </div>
                        <div class="vacio_info text-muted py-5" v-else>
                            <i class="fa fa-warehouse fs-1"></i>
                            <div>Selecciona una participante</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="card-title text-white pt-1">
                                    <i class="fa fa-clipboard-check"></i> Datos
                                    de la Pago
                                    <span v-if="form.id != 0" class="fw-bold"
                                        >- ACTUALIZACIÓN</span
                                    >
                                </h4>
                                <div
                                    class="float-end badge bgActivo rounded-circle fs-6"
                                >
                                    {{ form.pago_detalles.length }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm w-100"
                                    :disabled="enviando"
                                    @click.prevent="enviarFormulario"
                                    v-html="textBtn"
                                ></button>
                                <button
                                    type="button"
                                    class="btn btn-default btn-sm w-100 mt-2 border"
                                    @click="cancelarPago"
                                    v-html="txtBtnCancelar"
                                ></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
