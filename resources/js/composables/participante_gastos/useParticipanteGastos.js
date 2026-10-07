import { useForm } from "@inertiajs/vue3";

export const useParticipanteGastos = () => {
    const initialState = {
        id: 0,
        participante_id: "",
        gasto_id: "",
        porcentaje: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setParticipanteGasto = (item) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarParticipanteGasto = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    return {
        form,
        setParticipanteGasto,
        limpiarParticipanteGasto,
    };
};
