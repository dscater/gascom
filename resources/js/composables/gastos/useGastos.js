import { useForm } from "@inertiajs/vue3";

export const useGastos = () => {
    const initialState = {
        id: 0,
        nombre: "",
        descripcion: "",
        _method: "POST",
    };

    const form = useForm({ ...initialState });

    const setGasto = (item) => {
        form.clearErrors();
        form.reset();
        Object.assign(form, item);
        form._method = "PUT";
    };

    const limpiarGasto = () => {
        form.clearErrors();
        form.reset();
        form.defaults({ ...initialState });
    };

    return {
        form,
        setGasto,
        limpiarGasto,
    };
};
