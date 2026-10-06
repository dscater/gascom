import { useForm } from "@inertiajs/vue3";

export const useParticipantes = () => {
    const initialState = {
        id: 0,
        nombre: "",
        correo: "",
        descripcion: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setParticipante = (item) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarParticipante = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    return {
        form,
        setParticipante,
        limpiarParticipante,
    };
};
