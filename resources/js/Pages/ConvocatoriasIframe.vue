<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import {ref, watch} from 'vue'

const props = defineProps({invitations: Array, errors: Object,});
console.log(props.invitations);
let invitation_id = ref(0);
const actualInvitations = ref(props.invitations);
const payload = ref([]);

const isModalOpen = ref(false);

const selectedItem = ref(null);
const previews = ref([]);

const selectItem = (invitation) => {
    selectedItem.value = invitation;
}

watch(() => selectedItem.value, (newSelectedItem) => {
    if (newSelectedItem) {
        invitation_id.value = newSelectedItem.id;
    }
});
watch(() => props.invitations.value, (newSelectedItem) => {
    if (newSelectedItem) {
        invitation_id.value = newSelectedItem.id;
    }
});

const getFileName = (file) => {
    const words = file.split('/');
    return words[words.length - 1]
}
const handleFileChange = (event, name) => {

    const selectedFile = event.target.files[0];
    if (selectedFile) {
        payload.value = [...payload.value, { name: name, file: selectedFile }];
    }
};
const openFile = (file) => {
    const url = URL.createObjectURL(file);
    window.open(url, '_blank');
};
const base64ToArrayBuffer = (base64Str) => {
    const binaryString = window.atob(base64Str);
    const binaryLen = binaryString.length;
    const bytes = new Uint8Array(binaryLen);

    for (let i = 0; i < binaryLen; i++) {
        bytes[i] = binaryString.charCodeAt(i);
    }

    return bytes;
};

const showDocument = (base64Str, contentType) => {
    const byteArray = base64ToArrayBuffer(base64Str);
    const blob = new Blob([byteArray], {type: contentType});
    const url = URL.createObjectURL(blob);

    window.open(url, '_blank');
};
const submit = async () => {
    try {
        let formData = new FormData();
        formData.append('invitation_id', invitation_id.value);
        payload.value.forEach((fileObj, index) => {
            formData.append(`files[${index}]`, fileObj.file);
            formData.append(`names[${index}]`, fileObj.name);
        });

        const response = await axios.post('/apply-supplier', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        if (window.confirm('Gracias por postularte. Revisa tus postulaciones')) {
            // Si el usuario hace clic en "Aceptar"
            location.reload();}

    } catch (error) {
        window.confirm('No fue posible realizar la potulacion')
        console.log('no pude')
        console.error(error);
    }
};


const searchQuery = ref('');
const currentView = ref('estado')
const fetchInvitations = async (query) => {
    try {
        const response = await axios.get('/convocatorias-filter', {
            params: {name: query},
        });
        actualInvitations.value = response.data;

    } catch (error) {
        console.error('Error fetching invitations:', error);
    }
};
const handleFilter = () => {
    fetchInvitations(searchQuery.value);
};
</script>

<template>
    <MainLayout>
        <div class="mb-4 px-6">
            <input @keyup.enter="handleFilter" v-model="searchQuery"
                   class="w-full rounded-3xl" type="text" placeholder="Buscar por empresa o referencia">
        </div>

        <div class="flex h-4/5 px-6 overflow-y-hidden mt-8 space-x-12">
            <!-- Sidebar con tarjetas -->
            <div class="w-2/5  overflow-y-auto pr-4">
                <div
                    v-for="(invitation, index) in actualInvitations"

                    @click="selectItem(invitation)"
                    :class="['p-4 mb-4 border-2 rounded-lg cursor-pointer', selectedItem && selectedItem.id === invitation.id ? 'border-blueFigma' : 'border-gray-400']"
                >
                    <p v-if="invitation.active" class="rounded-3xl bg-lblue font-semibold text-black w-fit p-2 mb-4">
                        Activa</p>
                    <h3 class="text-lg font-semibold">{{ invitation.name }}</h3>
                    <p class="text-gray-500">Fecha Limite: {{ invitation.date_finish }}</p>
                </div>
            </div>

            <!-- Cuadro de descripción a la derecha -->
            <div class="w-2/3 p-4 border rounded-2xl h-full border-2 border-gray-400 overflow-y-auto">
                <form @submit.prevent="submit">
                    <div v-if="selectedItem">
                        <h2 class="text-2xl font-bold mb-4">{{ selectedItem.name }}</h2>

                        <p class="text-gray-700 mb-4">{{ selectedItem.description }}</p>

                        <p class="font-bold">Ten en cuenta la siguiente documentación para la postulacion </p>
                        <div v-for="file in JSON.parse(selectedItem.files)">

                            <a class="text-blue font-semibold cursor-pointer" :href="'/storage/'+file"
                               target="_blank">{{ getFileName(file) }}</a>
                        </div>
                        <div v-show="selectedItem.postulations.length === 0">
                            <div class="mb-4">
                                <div v-for="(requirement, index) in selectedItem.requirements">

                                    <label for="file" class="mr-2">Cargar el documento:
                                        {{ requirement.description }}</label>

                                    <input type="file" required id="file" accept="*/*"
                                           @change="(event) => handleFileChange(event,requirement.description)">

                                    <div v-if="payload.length">
                                        <h3>Archivo seleccionado:</h3>
                                        <div class="preview-container">
                                            <a href="#" @click.prevent="openFile(payload[0].file)">{{ payload[0].file.name }}</a>

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="flex gap-4">
                                <button data-modal-target="default-modal" data-modal-toggle="default-modal"
                                        type="submit"
                                        class="bg-blueFigma text-white px-4 py-2 rounded">Postularme
                                </button>
                            </div>

                        </div>

                        <!--                        Estado postulaciones-->
                        <div class="p-6 border rounded-lg" v-show="selectedItem.postulations.length > 0 ">
                            <!-- Tabs -->
                            <div class="flex mb-6">
                                <button
                                    :class="currentView === 'estado' ? 'bg-blueFigma text-white' : 'bg-blueFigma bg-opacity-60 text-white'"
                                    class="px-4 py-2 font-semibold  focus:outline-none rounded-l-xl"
                                    @click="currentView = 'estado'"
                                >
                                    Estado
                                </button>
                                <button
                                    :class="currentView === 'documentos' ? 'bg-blueFigma text-white' : 'bg-blueFigma bg-opacity-60 text-white'"
                                    class="px-4 py-2 font-semibold  focus:outline-none rounded-r-xl"
                                    @click="currentView = 'documentos'"
                                >
                                    Documentos
                                </button>
                            </div>

                            <!-- Vista del historial de postulacion (estado) -->
                            <div v-if="currentView === 'estado'">
                                <div class="relative">
                                    <!-- Postulado -->
                                    <div class="flex flex-col mb-8 align-baseline h-fit">
                                        <div class="flex  items-center">
                                            <span class="w-4 h-4 bg-green-500 rounded-full inline-block"></span>
                                            <h3 class="text-lg font-semibold">Postulado</h3>
                                        </div>
                                        <div class="flex relative items-center">
                                            <p class="text-gray-500 border p-3 rounded-lg bg-gray-100 ml-6">
                                                Tus documentos están en proceso de revisión, te invitamos a que
                                                consultes el estado de tu postulación en las próximas horas.
                                            </p>
                                            <div class="absolute h-2/3 w-0.5 bg-gray-300 mr-8"></div>

                                        </div>
                                    </div>

                                    <!-- Documentos aprobados -->
                                    <div class="flex items-start mb-8">
                                        <div class="flex-shrink-0">
                                            <span class="w-4 h-4 bg-blue-500 rounded-full inline-block"></span>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold">Documentos aprobados</h3>
                                        </div>
                                    </div>

                                    <!-- En contacto -->
                                    <div class="flex items-start mb-8">
                                        <div class="flex-shrink-0">
                                            <span class="w-4 h-4 bg-blue-500 rounded-full inline-block"></span>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold">En contacto</h3>
                                        </div>
                                    </div>

                                    <!-- Contratado -->
                                    <div class="flex items-start mb-8">
                                        <div class="flex-shrink-0">
                                            <span class="w-4 h-4 bg-blue-500 rounded-full inline-block"></span>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-semibold">Contratado</h3>
                                        </div>
                                    </div>

                                    <!-- Line -->
                                    <!--                                    <div class="absolute top-4 left-2.5 h-full w-0.5 bg-gray-300"></div>-->
                                </div>
                            </div>

                            <!-- Vista de documentos -->
                            <div v-if="currentView === 'documentos'">
                                <div class="border p-4 rounded-lg bg-gray-100">
                                    <h3 class="text-lg font-semibold">Documentos Subidos</h3>
                                    <div v-for="file in JSON.parse(selectedItem.files)"></div>
                                    <ul class="list-disc ml-6 text-gray-600">
                                        <li>Documento de identidad</li>
                                        <li>Certificado de estudios</li>
                                        <li>Currículum Vitae</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Cancelar postulación button -->
                            <button class="mt-6 px-6 py-3 bg-red-600 text-white font-bold rounded-full">
                                Cancelar postulación
                            </button>
                        </div>
                    </div>
                    <div v-else>
                        <p class="text-gray-500">Selecciona una convocatoria para ver los detalles.</p>
                    </div>

                </form>

            </div>
        </div>
    </MainLayout>
</template>

