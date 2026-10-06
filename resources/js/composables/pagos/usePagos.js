import { useForm } from "@inertiajs/vue3";

export const usePagos = () => {
    const initialState = {
        id: 0,
        mes: "",
        anio: "",
        total: "",
        pago_detalles: [],
        pago_participantes: [],
        eliminados_detalles: [],
        eliminados_participantes: [],
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setPago = (item) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarPago = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    return {
        form,
        setPago,
        limpiarPago,
    };
};
