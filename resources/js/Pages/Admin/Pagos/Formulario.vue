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

const listMeses = ref([
    {
        value: "01",
        label: "Enero",
    },
    {
        value: "02",
        label: "Febrero",
    },
    {
        value: "03",
        label: "Marzo",
    },
    {
        value: "04",
        label: "Abril",
    },
    {
        value: "05",
        label: "Mayo",
    },
    {
        value: "06",
        label: "Junio",
    },
    {
        value: "07",
        label: "Julio",
    },
    {
        value: "08",
        label: "Agosto",
    },
    {
        value: "09",
        label: "Septiembre",
    },
    {
        value: "10",
        label: "Octubre",
    },
    {
        value: "11",
        label: "Noviembre",
    },
    {
        value: "12",
        label: "Diciembre",
    },
]);
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

const agregarPagoDetalle = () => {
    form.pago_detalles.push({
        id: 0,
        pago_id: 0,
        gasto_id: "",
        monto: 0,
        fecha: "",
    });
};

const agregarParticipante = () => {
    form.pago_participantes.push({
        id: 0,
        pago_id: 0,
        participante_id: "",
    });
};

const quitarPagoDetalle = (item, index) => {
    if (item.id != 0) {
        form.eliminados_detalles.push(item.id);
    }

    form.pago_detalles.splice(index, 1);
};

const quitarPagoParticipante = (item, index) => {
    if (item.id != 0) {
        form.eliminados_participantes.push(item.id);
    }

    form.pago_participantes.splice(index, 1);
};

const totalPago = computed(() => {
    return form.pago_detalles.reduce((acc, item) => {
        return acc + parseFloat(item.monto ?? 0);
    }, 0);
});

watch([totalPago], (newTotalPago) => {
    form.total = parseFloat(newTotalPago);
});

onMounted(() => {
    cargarListas();
});
</script>

<template>
    <form @submit.prevent="enviarFormulario()">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg1">
                        <h4 class="card-title text-white">
                            <i class="fa fa-clipboard-list"></i> Datos del Pago
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="required">Mes</label>
                                <el-select
                                    v-model="form.mes"
                                    placeholder="Mes"
                                    no-data-text="Sin datos"
                                    no-match-text="Sin resultados"
                                    filterable
                                >
                                    <el-option
                                        v-for="item in listMeses"
                                        :key="item.value"
                                        :value="item.value"
                                        :label="item.label"
                                    ></el-option>
                                </el-select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="required">Año</label>
                                <input
                                    type="text"
                                    v-model="form.anio"
                                    class="form-control"
                                    placeholder="Año"
                                />
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="required">Total</label>
                                <input
                                    type="number"
                                    v-model="totalPago"
                                    class="form-control"
                                    placeholder="Total"
                                    readonly
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header bg1">
                        <h4 class="card-title text-white">
                            <i class="fa fa-table"></i> Detalles del pago
                            <button
                                type="button"
                                class="btn btn-primary btn-sm fs-7"
                                @click.prevent="agregarPagoDetalle"
                            >
                                <i class="fa fa-plus"></i> Agregar Detalle
                            </button>
                        </h4>
                    </div>
                    <div class="card-body bg-dark-gray">
                        <div class="row">
                            <div
                                class="col-6"
                                v-for="(item, index) in form.pago_detalles"
                            >
                                <div class="card mt-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm fs-8"
                                                    @click.prevent="
                                                        quitarPagoDetalle(
                                                            item,
                                                            index,
                                                        )
                                                    "
                                                >
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="required"
                                                    >Gasto</label
                                                >
                                                <el-select
                                                    v-model="item.gasto_id"
                                                    placeholder="Gasto"
                                                    filterable
                                                    no-data-text="Sin datos"
                                                    no-match-text="Sin resultados"
                                                >
                                                    <el-option
                                                        v-for="item in listGastos"
                                                        :key="item.id"
                                                        :value="item.id"
                                                        :label="item.nombre"
                                                    ></el-option
                                                ></el-select>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label class="required"
                                                    >Monto</label
                                                >
                                                <input
                                                    type="number"
                                                    v-model="item.monto"
                                                    class="form-control"
                                                    placeholder="Monto"
                                                />
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Fecha</label>
                                                <input
                                                    type="date"
                                                    v-model="item.fecha"
                                                    class="form-control"
                                                    placeholder="Fecha"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="col-12"
                                v-if="form.errors?.pago_detalles"
                            >
                                <ul class="d-block text-danger list-unstyled">
                                    <li class="parsley-required">
                                        {{ form.errors?.pago_detalles }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header bg1">
                        <h4 class="card-title text-white">
                            <i class="fa fa-user-friends"></i> Participantes
                            <button
                                type="button"
                                class="btn btn-primary btn-sm fs-7"
                                @click.prevent="agregarParticipante"
                            >
                                <i class="fa fa-plus"></i> Agregar Participante
                            </button>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div
                                class="col-sm-6 form-group"
                                v-for="(item, index) in form.pago_participantes"
                            >
                                <label class="required">Participante</label>
                                <div class="input-group">
                                    <div class="form-control border-0 p-0">
                                        <el-select
                                            v-model="item.participante_id"
                                            class="el-select-input-group-left"
                                            size="large"
                                            placeholder="Participante"
                                            filterable
                                            no-data-text="Sin datos"
                                            no-match-text="Sin resultados"
                                        >
                                            <el-option
                                                v-for="item in listParticipantes"
                                                :key="item.id"
                                                :value="item.id"
                                                :label="item.nombre"
                                            ></el-option
                                        ></el-select>
                                    </div>
                                    <div class="input-group-text p-0">
                                        <button
                                            type="button"
                                            class="btn btn-danger fs-8 rounded-0 h-100"
                                            @click.prevent="
                                                quitarPagoParticipante(
                                                    item,
                                                    index,
                                                )
                                            "
                                        >
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="col-12"
                                v-if="form.errors?.pago_participantes"
                            >
                                <ul class="d-block text-danger list-unstyled">
                                    <li class="parsley-required">
                                        {{ form.errors?.pago_participantes }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-2">
                <button
                    class="btn btn-primary float-end btn-lg"
                    :disabled="enviando"
                    @click="enviarFormulario"
                    v-html="textBtn"
                ></button>
            </div>
        </div>
    </form>
</template>
