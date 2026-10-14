<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const emits = defineEmits(["envio-formulario"]);

const form = props.form;

const enviando = ref(false);
const listMeses = ref([
    { value: "01", label: "Enero" },
    { value: "02", label: "Febrero" },
    { value: "03", label: "Marzo" },
    { value: "04", label: "Abril" },
    { value: "05", label: "Mayo" },
    { value: "06", label: "Junio" },
    { value: "07", label: "Julio" },
    { value: "08", label: "Agosto" },
    { value: "09", label: "Septiembre" },
    { value: "10", label: "Octubre" },
    { value: "11", label: "Noviembre" },
    { value: "12", label: "Diciembre" },
]);

const listParticipantes = ref([]);
const listGastos = ref([]);

let contadorTemporal = 0;

const nuevaClave = (prefijo) => {
    contadorTemporal++;
    return `${prefijo}_${Date.now()}_${contadorTemporal}`;
};

const asegurarEstructura = () => {
    if (!Array.isArray(form.pago_detalles)) {
        form.pago_detalles = [];
    }

    if (!Array.isArray(form.pago_participantes)) {
        form.pago_participantes = [];
    }

    if (!Array.isArray(form.pago_gastos)) {
        form.pago_gastos = [];
    }

    if (!Array.isArray(form.eliminados_detalles)) {
        form.eliminados_detalles = [];
    }

    if (!Array.isArray(form.eliminados_participantes)) {
        form.eliminados_participantes = [];
    }

    form.pago_detalles.forEach((detalle) => {
        if (!detalle._key) {
            detalle._key = nuevaClave("detalle");
        }
    });

    form.pago_participantes.forEach((participante) => {
        if (!participante._key) {
            participante._key = nuevaClave("participante");
        }
    });

    form.pago_gastos.forEach((gasto) => {
        if (!gasto._key) {
            gasto._key = nuevaClave("distribucion");
        }
    });
};

// Identifica una distribución mediante IDs reales cuando existen.
// Si todavía son registros nuevos, utiliza las claves temporales.
const buscarDistribucion = (detalle, participante) => {
    return form.pago_gastos.find((gasto) => {
        const detalleCoincide =
            Number(detalle.id) > 0
                ? Number(gasto.pago_detalle_id) === Number(detalle.id)
                : gasto._detalle_key === detalle._key;

        const participanteCoincide =
            Number(participante.id) > 0
                ? Number(gasto.pago_participante_id) === Number(participante.id)
                : gasto._participante_key === participante._key;

        return detalleCoincide && participanteCoincide;
    });
};

const crearDistribucion = (detalle, participante) => {
    const gasto = {
        _key: nuevaClave("distribucion"),
        _detalle_key: detalle._key,
        _participante_key: participante._key,

        pago_id: form.id || 0,
        pago_detalle_id: detalle.id || 0,
        pago_participante_id: participante.id || 0,

        participante_id: participante.participante_id || 0,
        gasto_id: detalle.gasto_id || 0,
        porcentaje_pago: 0,
        monto_pagado: 0,
        estado: "PENDIENTE",
    };

    form.pago_gastos.push(gasto);

    return gasto;
};

const obtenerDistribucion = (detalle, participante) => {
    let gasto = buscarDistribucion(detalle, participante);

    if (!gasto) {
        gasto = crearDistribucion(detalle, participante);
    }

    // Mantener las claves temporales para los registros nuevos.
    gasto._detalle_key ??= detalle._key;
    gasto._participante_key ??= participante._key;

    return gasto;
};

const asegurarDistribuciones = () => {
    asegurarEstructura();

    for (const detalle of form.pago_detalles) {
        for (const participante of form.pago_participantes) {
            obtenerDistribucion(detalle, participante);
        }
    }
};

// ------------------------------------------------------------------
// CARGA DE LISTAS
// ------------------------------------------------------------------

const cargarParticipantes = async () => {
    try {
        const res = await axios.get(route("participantes.listado"));
        listParticipantes.value = res.data.participantes ?? [];
    } catch (error) {
        console.error(error);
        toast.error("No se pudieron cargar los participantes");
    }
};

const cargarGastos = async () => {
    try {
        const res = await axios.get(route("gastos.listado"));
        listGastos.value = res.data.gastos ?? [];
    } catch (error) {
        console.error(error);
        toast.error("No se pudieron cargar los gastos");
    }
};

const cargarListas = async () => {
    await Promise.all([cargarParticipantes(), cargarGastos()]);
};

// ------------------------------------------------------------------
// AGREGAR Y ELIMINAR DETALLES
// ------------------------------------------------------------------

const agregarPagoDetalle = () => {
    const detalle = {
        _key: nuevaClave("detalle"),
        id: 0,
        pago_id: form.id || 0,
        gasto_id: "",
        monto: 0,
        fecha: "",
    };

    form.pago_detalles.push(detalle);

    // Crear las celdas de la nueva fila.
    for (const participante of form.pago_participantes) {
        crearDistribucion(detalle, participante);
    }
};

const agregarParticipante = () => {
    const participante = {
        _key: nuevaClave("participante"),
        id: 0,
        pago_id: form.id || 0,
        participante_id: "",
    };

    form.pago_participantes.push(participante);

    // Crear las celdas de la nueva columna.
    for (const detalle of form.pago_detalles) {
        crearDistribucion(detalle, participante);
    }
};

const quitarPagoDetalle = (detalle, index) => {
    if (Number(detalle.id) > 0) {
        if (!form.eliminados_detalles.includes(detalle.id)) {
            form.eliminados_detalles.push(detalle.id);
        }
    }

    form.pago_gastos = form.pago_gastos.filter(
        (gasto) => gasto._detalle_key !== detalle._key,
    );

    form.pago_detalles.splice(index, 1);
};

const quitarPagoParticipante = (participante, index) => {
    if (Number(participante.id) > 0) {
        if (!form.eliminados_participantes.includes(participante.id)) {
            form.eliminados_participantes.push(participante.id);
        }
    }

    form.pago_gastos = form.pago_gastos.filter(
        (gasto) => gasto._participante_key !== participante._key,
    );

    form.pago_participantes.splice(index, 1);
};

// ------------------------------------------------------------------
// MATRIZ
// ------------------------------------------------------------------

const matrizGastos = computed(() => {
    return form.pago_detalles.map((detalle) => {
        return {
            detalle,
            participantes: form.pago_participantes.map((participante) => {
                return {
                    participante,
                    gasto: buscarDistribucion(detalle, participante) ?? {
                        pago_id: form.id || 0,
                        pago_detalle_id: detalle.id || 0,
                        pago_participante_id: participante.id || 0,
                        gasto_id: detalle.gasto_id || 0,
                        porcentaje_pago: 0,
                        monto_pagado: 0,
                        estado: "PENDIENTE",
                    },
                };
            }),
        };
    });
});

// ------------------------------------------------------------------
// TOTALES
// ------------------------------------------------------------------

const numero = (valor) => {
    const resultado = Number(valor);
    return Number.isFinite(resultado) ? resultado : 0;
};

const totalPago = computed(() => {
    return form.pago_detalles.reduce(
        (total, detalle) => total + numero(detalle.monto),
        0,
    );
});

watch(
    totalPago,
    (nuevoTotal) => {
        form.total = Number(nuevoTotal.toFixed(2));
    },
    { immediate: true },
);

const totalesFilas = computed(() => {
    const totales = {};

    for (const fila of matrizGastos.value) {
        totales[fila.detalle._key] = fila.participantes.reduce(
            (total, item) => total + numero(item.gasto.monto_pagado),
            0,
        );
    }

    return totales;
});

const totalesColumnas = computed(() => {
    const totales = {};

    for (const participante of form.pago_participantes) {
        totales[participante._key] = 0;
    }

    for (const fila of matrizGastos.value) {
        for (const item of fila.participantes) {
            const key = item.participante._key;

            totales[key] =
                (totales[key] ?? 0) + numero(item.gasto.monto_pagado);
        }
    }

    return totales;
});

const formatoMonto = (valor) => numero(valor).toFixed(2);

// ------------------------------------------------------------------
// CÁLCULO DE PORCENTAJE Y MONTO
// ------------------------------------------------------------------

const cambiarPorcentaje = (item, detalle) => {
    const montoDetalle = numero(detalle.monto);
    const porcentaje = numero(item.gasto.porcentaje_pago);

    item.gasto.monto_pagado = Number(
        ((montoDetalle * porcentaje) / 100).toFixed(2),
    );

    item.gasto.gasto_id = detalle.gasto_id || 0;
};

const cambiarMonto = (item, detalle) => {
    const montoDetalle = numero(detalle.monto);
    const monto = numero(item.gasto.monto_pagado);

    item.gasto.porcentaje_pago =
        montoDetalle > 0
            ? Number(((monto / montoDetalle) * 100).toFixed(8))
            : 0;

    item.gasto.gasto_id = detalle.gasto_id || 0;
};

// Si cambia el importe del detalle, recalcular los importes
// conservando los porcentajes que tiene cada participante.
const recalcularImportesFila = (fila) => {
    for (const item of fila.participantes) {
        cambiarPorcentaje(item, fila.detalle);
    }
};

// ------------------------------------------------------------------
// CONSULTA DE PARTICIPANTEGASTO
// ------------------------------------------------------------------

const cargarPorcentaje = async (item, detalle) => {
    const participanteId = item.participante.participante_id;
    const gastoId = detalle.gasto_id;

    item.gasto.participante_id = participanteId || null;
    item.gasto.gasto_id = gastoId || 0;

    if (!participanteId || !gastoId) {
        item.gasto.porcentaje_pago = 0;
        item.gasto.monto_pagado = 0;
        return;
    }

    // Evitar aplicar una respuesta antigua si el usuario
    // cambia rápidamente de participante o de gasto.
    const solicitud = nuevaClave("consulta");
    item.gasto._solicitud_porcentaje = solicitud;

    try {
        const res = await axios.get(route("participante_gastos.porcentaje"), {
            params: {
                participante_id: participanteId,
                gasto_id: gastoId,
            },
        });

        if (item.gasto._solicitud_porcentaje !== solicitud) {
            return;
        }

        // Solo actualizar la distribución que corresponde
        // a la selección que originó la consulta.
        if (
            Number(item.participante.participante_id) !==
                Number(participanteId) ||
            Number(detalle.gasto_id) !== Number(gastoId)
        ) {
            return;
        }

        item.gasto.porcentaje_pago = numero(res.data.porcentaje);

        cambiarPorcentaje(item, detalle);
    } catch (error) {
        console.error(error);
        toast.error("No se pudo consultar el porcentaje configurado");
    }
};

const cargarPorcentajesFila = async (fila) => {
    await Promise.all(
        fila.participantes.map((item) => cargarPorcentaje(item, fila.detalle)),
    );
};

const cambiarGastoDetalle = async (fila) => {
    fila.detalle.gasto_id = fila.detalle.gasto_id || "";

    for (const item of fila.participantes) {
        item.gasto.gasto_id = fila.detalle.gasto_id || 0;
    }

    await cargarPorcentajesFila(fila);
};

const cambiarParticipante = async (fila, item) => {
    await cargarPorcentaje(item, fila.detalle);
};

// ------------------------------------------------------------------
// CANCELAR
// ------------------------------------------------------------------

const cancelarPago = async () => {
    const message =
        form.id == 0
            ? "Se cancelará el registro del pago."
            : `Se cancelará la actualización del pago ${form.id}.`;

    const result = await Swal.fire({
        title: "¿Cancelar registro?",
        html: message,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "No, continuar",
        customClass: {
            confirmButton: "btn-danger",
        },
    });

    if (!result.isConfirmed) {
        return;
    }

    if (form.id != 0) {
        router.get(route("pagos.index"));
        return;
    }

    form.reset();

    asegurarEstructura();
    emits("envio-formulario");
};

// ------------------------------------------------------------------
// ENVIAR FORMULARIO
// ------------------------------------------------------------------

const textBtn = computed(() => {
    if (enviando.value) {
        return '<i class="fa fa-spin fa-spinner"></i> Enviando...';
    }

    return form.id == 0
        ? '<i class="fa fa-save"></i> Registrar Pago'
        : '<i class="fa fa-edit"></i> Actualizar Pago';
});

const enviarFormulario = () => {
    if (enviando.value) {
        return;
    }

    asegurarEstructura();

    form.total = Number(totalPago.value.toFixed(2));

    enviando.value = true;

    const url =
        form.id == 0 ? route("pagos.store") : route("pagos.update", form.id);

    form.post(url, {
        preserveScroll: true,
        forceFormData: true,

        onSuccess: (page) => {
            const success =
                page.props.flash?.success ?? "Proceso realizado con éxito";

            const urlBlank = page.props.url_blank ?? null;

            Swal.fire({
                icon: "success",
                title: "Correcto",
                html: `<strong>${success}</strong>`,
                confirmButtonText: "Aceptar",
                customClass: {
                    confirmButton: "btn-alert-success",
                },
            });

            if (urlBlank) {
                window.open(urlBlank, "_blank");
            }

            emits("envio-formulario");
        },

        onError: () => {
            if (Object.keys(form.errors).length > 0) {
                const errores = Object.values(form.errors)
                    .map((error) => `<li>${error}</li>`)
                    .join("");

                Swal.fire({
                    icon: "error",
                    title: "Errores de validación",
                    html: `
                        <p>Existen errores en el formulario:</p>
                        <ul style="text-align:left">${errores}</ul>
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
                    text: "Ocurrió un error inesperado. Contacte con el administrador.",
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

onMounted(async () => {
    asegurarEstructura();
    await cargarListas();
});
</script>

<template>
    <form @submit.prevent="enviarFormulario">
        <div class="row">
            <div class="col-12">
                <!-- DATOS DEL PAGO -->
                <div class="card">
                    <div class="card-header bg1">
                        <h4 class="card-title text-white">
                            <i class="fa fa-clipboard-list"></i>
                            Datos del Pago
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
                                    class="w-100"
                                >
                                    <el-option
                                        v-for="mes in listMeses"
                                        :key="mes.value"
                                        :value="mes.value"
                                        :label="mes.label"
                                    />
                                </el-select>
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="required">Año</label>

                                <input
                                    type="number"
                                    v-model.number="form.anio"
                                    class="form-control"
                                    placeholder="Año"
                                />
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="required">Total</label>

                                <input
                                    type="number"
                                    :value="formatoMonto(totalPago)"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MATRIZ DE GASTOS -->
                <div class="card mt-2">
                    <div class="card-header">
                        <button
                            type="button"
                            class="btn btn-dark btn-sm me-1"
                            @click="agregarPagoDetalle"
                        >
                            <i class="fa fa-plus"></i>
                            Agregar Detalle
                        </button>

                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            @click="agregarParticipante"
                        >
                            <i class="fa fa-plus"></i>
                            Agregar Participante
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-striped align-middle"
                            >
                                <thead>
                                    <tr>
                                        <th style="min-width: 260px">Gasto</th>

                                        <th
                                            v-for="participante in form.pago_participantes"
                                            :key="participante._key"
                                            style="min-width: 270px"
                                        >
                                            <div class="d-flex gap-1">
                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm"
                                                    @click="
                                                        quitarPagoParticipante(
                                                            participante,
                                                            form.pago_participantes.indexOf(
                                                                participante,
                                                            ),
                                                        )
                                                    "
                                                >
                                                    <i class="fa fa-times"></i>
                                                </button>

                                                <el-select
                                                    v-model="
                                                        participante.participante_id
                                                    "
                                                    placeholder="Participante"
                                                    filterable
                                                    class="w-100"
                                                    no-data-text="Sin datos"
                                                    no-match-text="Sin resultados"
                                                    @change="
                                                        async () => {
                                                            for (const fila of matrizGastos) {
                                                                const item =
                                                                    fila.participantes.find(
                                                                        (
                                                                            celda,
                                                                        ) =>
                                                                            celda
                                                                                .participante
                                                                                ._key ===
                                                                            participante._key,
                                                                    );

                                                                if (item) {
                                                                    await cambiarParticipante(
                                                                        fila,
                                                                        item,
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    "
                                                >
                                                    <el-option
                                                        v-for="p in listParticipantes"
                                                        :key="p.id"
                                                        :value="p.id"
                                                        :label="p.nombre"
                                                    />
                                                </el-select>
                                            </div>
                                        </th>

                                        <th style="min-width: 120px">TOTAL</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr
                                        v-for="fila in matrizGastos"
                                        :key="fila.detalle._key"
                                    >
                                        <!-- DETALLE -->
                                        <td class="bg-dark text-white">
                                            <div class="mb-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm"
                                                    @click="
                                                        quitarPagoDetalle(
                                                            fila.detalle,
                                                            form.pago_detalles.indexOf(
                                                                fila.detalle,
                                                            ),
                                                        )
                                                    "
                                                >
                                                    <i class="fa fa-times"></i>
                                                    Quitar
                                                </button>
                                            </div>

                                            <div class="mb-2">
                                                <el-select
                                                    v-model="
                                                        fila.detalle.gasto_id
                                                    "
                                                    placeholder="Seleccionar gasto"
                                                    filterable
                                                    class="w-100"
                                                    no-data-text="Sin datos"
                                                    no-match-text="Sin resultados"
                                                    @change="
                                                        cambiarGastoDetalle(
                                                            fila,
                                                        )
                                                    "
                                                >
                                                    <el-option
                                                        v-for="g in listGastos"
                                                        :key="g.id"
                                                        :value="g.id"
                                                        :label="g.nombre"
                                                    />
                                                </el-select>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">
                                                    Monto
                                                </label>

                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    v-model.number="
                                                        fila.detalle.monto
                                                    "
                                                    class="form-control"
                                                    placeholder="Monto"
                                                    @input="
                                                        recalcularImportesFila(
                                                            fila,
                                                        )
                                                    "
                                                />
                                            </div>

                                            <div>
                                                <label class="form-label">
                                                    Fecha
                                                </label>

                                                <input
                                                    type="date"
                                                    v-model="fila.detalle.fecha"
                                                    class="form-control"
                                                />
                                            </div>
                                        </td>

                                        <!-- PARTICIPANTES -->
                                        <td
                                            v-for="item in fila.participantes"
                                            :key="item.participante._key"
                                        >
                                            <div class="mb-2">
                                                <label class="form-label">
                                                    Porcentaje (%)
                                                </label>

                                                <div class="input-group">
                                                    <span
                                                        class="input-group-text"
                                                    >
                                                        <i
                                                            class="fa fa-percent"
                                                        ></i>
                                                    </span>

                                                    <input
                                                        type="number"
                                                        min="0"
                                                        step="any"
                                                        v-model.number="
                                                            item.gasto
                                                                .porcentaje_pago
                                                        "
                                                        class="form-control"
                                                        @input="
                                                            cambiarPorcentaje(
                                                                item,
                                                                fila.detalle,
                                                            )
                                                        "
                                                    />
                                                </div>
                                            </div>

                                            <div>
                                                <label class="form-label">
                                                    Monto (Bs.)
                                                </label>

                                                <div class="input-group">
                                                    <span
                                                        class="input-group-text"
                                                    >
                                                        Bs.
                                                    </span>

                                                    <input
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        v-model.number="
                                                            item.gasto
                                                                .monto_pagado
                                                        "
                                                        class="form-control"
                                                        @input="
                                                            cambiarMonto(
                                                                item,
                                                                fila.detalle,
                                                            )
                                                        "
                                                    />
                                                </div>

                                                <small class="text-muted">
                                                    Configurado:
                                                    {{
                                                        item.gasto
                                                            .porcentaje_pago
                                                    }}%
                                                </small>
                                            </div>
                                        </td>

                                        <!-- TOTAL POR DETALLE -->
                                        <td
                                            class="fw-bold bg-primary text-white"
                                        >
                                            Bs.
                                            {{
                                                formatoMonto(
                                                    totalesFilas[
                                                        fila.detalle._key
                                                    ],
                                                )
                                            }}
                                        </td>
                                    </tr>

                                    <tr v-if="form.pago_detalles.length === 0">
                                        <td
                                            :colspan="
                                                form.pago_participantes.length +
                                                2
                                            "
                                            class="text-center text-muted"
                                        >
                                            Agrega al menos un detalle de gasto.
                                        </td>
                                    </tr>
                                </tbody>

                                <!-- TOTALES -->
                                <tfoot>
                                    <tr>
                                        <th class="bg-primary text-white">
                                            TOTAL
                                        </th>

                                        <th
                                            class="bg-primary text-white"
                                            v-for="participante in form.pago_participantes"
                                            :key="participante._key"
                                        >
                                            Bs.
                                            {{
                                                formatoMonto(
                                                    totalesColumnas[
                                                        participante._key
                                                    ],
                                                )
                                            }}
                                        </th>

                                        <th class="bg-primary text-white">
                                            Bs.
                                            {{ formatoMonto(totalPago) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- ERRORES -->
                    <div
                        v-if="form.errors?.pago_detalles"
                        class="col-12 px-3 pb-2 text-danger"
                    >
                        {{ form.errors.pago_detalles }}
                    </div>

                    <div
                        v-if="form.errors?.pago_participantes"
                        class="col-12 px-3 pb-2 text-danger"
                    >
                        {{ form.errors.pago_participantes }}
                    </div>

                    <div
                        v-if="form.errors?.pago_gastos"
                        class="col-12 px-3 pb-2 text-danger"
                    >
                        {{ form.errors.pago_gastos }}
                    </div>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="col-12 mt-3 d-flex justify-content-end gap-2">
                <button
                    type="button"
                    class="btn btn-secondary btn-lg"
                    :disabled="enviando"
                    @click="cancelarPago"
                >
                    <i class="fa fa-times"></i>
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="btn btn-primary btn-lg"
                    :disabled="enviando"
                    v-html="textBtn"
                />
            </div>
        </div>
    </form>
</template>
