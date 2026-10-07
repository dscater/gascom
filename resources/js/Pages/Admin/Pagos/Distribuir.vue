<script setup>
import Content from "@/Components/Content.vue";
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { usePagos } from "@/composables/pagos/usePagos";
import { ref, onMounted, onBeforeMount, computed } from "vue";
import { useAppStore } from "@/stores/aplicacion/appStore";
import Formulario from "./Formulario.vue";
const props = defineProps({
    pago: {
        type: Object,
        required: true,
    },
});
const { props: props_page } = usePage();
const appStore = useAppStore();
const { setPago, limpiarPago, form } = usePagos();
onBeforeMount(() => {
    setPago(props.pago);
    appStore.startLoading();
});

const enviando = ref(false);
const enviarFormulario = () => {
    enviando.value = true;
    let url = route("pagos.guardar_distribuir", props.pago.id);
    const form = useForm({
        matrizGastos: matrizGastos.value,
        _method: "PUT",
    });

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (response) => {
            console.log("correcto");
            const success =
                response.props.flash.success ?? "Proceso realizado con éxito";
            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                confirmButtonText: `Aceptar`,
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });

            document
                .getElementsByTagName("body")[0]
                .classList.remove("modal-open");
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

const gastosIndexados = computed(() => {
    const mapa = {};
    for (const gasto of props.pago.pago_gastos) {
        const key = `${gasto.pago_detalle_id}_${gasto.pago_participante_id}`;
        mapa[key] = gasto;
    }

    return mapa;
});

const totalesFilas = computed(() => {
    const totales = {};

    for (const fila of matrizGastos.value) {
        totales[fila.detalle.id] = fila.participantes.reduce((total, item) => {
            return total + Number(item.gasto?.monto_pagado ?? 0);
        }, 0);
    }

    return totales;
});

const totalesColumnas = computed(() => {
    const totales = {};

    for (const participante of props.pago.pago_participantes) {
        totales[participante.id] = 0;
    }

    for (const fila of matrizGastos.value) {
        for (const item of fila.participantes) {
            totales[item.participante.id] += Number(
                item.gasto?.monto_pagado ?? 0,
            );
        }
    }

    return totales;
});

const matrizGastos = computed(() => {
    return props.pago.pago_detalles.map((detalle) => {
        return {
            detalle,

            participantes: props.pago.pago_participantes.map((participante) => {
                const key = `${detalle.id}_${participante.id}`;

                return {
                    participante,
                    gasto: gastosIndexados.value[key] ?? {
                        pago_id: props.pago.id,
                        pago_detalle_id: detalle.id,
                        pago_participante_id: participante.id,
                        gasto_id: detalle.gasto_id,
                        porcentaje_pago: 0,
                        monto_pagado: 0,
                        estado: "PENDIENTE",
                    },
                };
            }),
        };
    });
});

const cambiarPorcentaje = (item, detalle) => {
    const montoDetalle = Number(detalle.monto ?? 0);
    const porcentaje = Number(item.gasto.porcentaje_pago ?? 0);

    item.gasto.monto_pagado =
        Math.round(((montoDetalle * porcentaje) / 100) * 100) / 100;
};

const cambiarMonto = (item, detalle) => {
    const montoDetalle = Number(detalle.monto ?? 0);
    const monto = Number(item.gasto.monto_pagado ?? 0);

    if (montoDetalle > 0) {
        item.gasto.porcentaje_pago =
            Math.round((monto / montoDetalle) * 100 * 1e8) / 1e8;
    } else {
        item.gasto.porcentaje_pago = 0;
    }
};

onMounted(() => {
    appStore.stopLoading();
});
</script>
<template>
    <Head title="Distribuir Gastos de Pago"></Head>
    <Content>
        <template #header>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">
                        <i class="fa fa-table"></i> Distribuir Gastos de Pago
                    </h3>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <Link :href="route('inicio')">Inicio</Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="route('pagos.index')">Pagos</Link>
                        </li>
                        <li class="breadcrumb-item active">
                            Distribuir Gastos de Pago
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </template>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Gasto</th>
                            <th
                                v-for="participante in props.pago
                                    .pago_participantes"
                                :key="participante.id"
                            >
                                {{ participante.participante.nombre }}
                            </th>

                            <th>TOTAL</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="fila in matrizGastos" :key="fila.detalle.id">
                            <!-- Detalle -->
                            <td>
                                <strong>
                                    {{ fila.detalle.gasto.nombre }}
                                </strong>

                                <br />

                                <small>
                                    Bs.
                                    {{ Number(fila.detalle.monto).toFixed(2) }}
                                </small>
                            </td>

                            <!-- Participantes -->
                            <td
                                v-for="item in fila.participantes"
                                :key="item.participante.id"
                            >
                                <div>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fa fa-percent"></i> </span
                                        ><input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="form-control"
                                            v-model.number="
                                                item.gasto.porcentaje_pago
                                            "
                                            @change="
                                                cambiarPorcentaje(
                                                    item,
                                                    fila.detalle,
                                                )
                                            "
                                            @keyup.enter="
                                                cambiarPorcentaje(
                                                    item,
                                                    fila.detalle,
                                                )
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <div class="input-group">
                                        <span
                                            class="input-group-text fs-8 fw-bold"
                                        >
                                            Bs.
                                        </span>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="form-control"
                                            v-model.number="
                                                item.gasto.monto_pagado
                                            "
                                            @change="
                                                cambiarMonto(item, fila.detalle)
                                            "
                                            @keyup.enter="
                                                cambiarMonto(item, fila.detalle)
                                            "
                                        />
                                    </div>
                                </div>
                            </td>
                            <!-- Total del detalle -->
                            <td>
                                Bs.
                                {{ totalesFilas[fila.detalle.id].toFixed(2) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>TOTAL</th>

                            <th
                                v-for="participante in props.pago
                                    .pago_participantes"
                                :key="participante.id"
                            >
                                Bs.
                                {{
                                    Number(
                                        totalesColumnas[participante.id] ?? 0,
                                    ).toFixed(2)
                                }}
                            </th>

                            <th>
                                Bs. {{ Number(props.pago.total).toFixed(2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-12">
                <button
                    class="btn btn-primary"
                    @click="enviarFormulario"
                    :disabled="enviando"
                >
                    <i class="fa fa-envelope"></i> Guardar y Enviar Gastos
                </button>
            </div>
        </div>
    </Content>
</template>
