<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h2>{{ $t('add_event') }}</h2>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-col cols="12">
            <v-text-field
              v-model="event.title"
              :label="$t('title')"
              :rules="[v => !!v || $t('title_required')]"
              required
            />
          </v-col>
          <v-col cols="12">
            <v-text-field
              v-model="event.event_date"
              :label="$t('date')"
              type="date"
              :rules="[v => !!v || $t('date_required')]"
              required
            />
          </v-col>
          <v-col cols="12">
            <v-textarea
              v-model="event.description"
              :label="$t('description')"
              :rules="[v => !!v || $t('description_required')]"
              required
            />
          </v-col>
          <v-col cols="12">
            <v-text-field
              v-model="event.location"
              :label="$t('location')"
              :rules="[v => !!v || $t('location_required')]"
              required
            />
          </v-col>
          <v-col cols="12">
            <v-text-field
              v-model="event.contact_email"
              :label="$t('contact_email')"
              :rules="[
                v => !!v || $t('email_required'),
                v => /.+@.+\..+/.test(v) || $t('email_invalid')
              ]"
              type="email"
              required
            />
          </v-col>
          <v-col cols="12">
            <v-text-field
              v-model="event.capacity"
              :label="$t('capacity')"
              :rules="[v => !!v || $t('capacity_required')]"
              type="number"
              required
            />
          </v-col>
          <v-col cols="12">
            <v-btn
              :disabled="!valid"
              color="green darken-1"
              @click="addEvent"
            >
              {{ $t('add_event_button') }}
            </v-btn>
          </v-col>
        </v-form>
      </v-col>
    </v-row>
  </v-container>
</template>


<script>
import { ref } from "vue";
import { useToast } from "vue-toastification";
import axios from "~/plugins/axios"; // Assurez-vous d'avoir configuré axios

export default {
  setup() {
    const event = ref({
      title: "", // Titre par défaut
      event_date: "", // Date de l'événement
      description: "", // Description par défaut
      location: "", // Lieu par défaut
      contact_email: "", // Email de contact
      capacity: null, // Capacité
    });

    const $axios = axios().provide.axios;
    const toast = useToast();
    const valid = ref(false);

    // Fonction pour ajouter un événement avec les champs multilingues
    const addEvent = async () => {
      if (valid.value) {
        // Préparer les champs multilingues pour title, location et description
        const eventData = {
          ...event.value, // Copier les données existantes
          title_fr: event.value.title, // Titre en français
          title_ar: event.value.title, // Titre en arabe
          title_en: event.value.title, // Titre en anglais
          location_fr: event.value.location, // Lieu en français
          location_ar: event.value.location, // Lieu en arabe
          location_en: event.value.location, // Lieu en anglais
          description_fr: event.value.description, // Description en français
          description_ar: event.value.description, // Description en arabe
          description_en: event.value.description, // Description en anglais
        };

        try {
          await $axios.post("/api/evenements", eventData); // Envoyer les données
          toast.success("Événement ajouté avec succès.");
          resetForm(); // Réinitialiser le formulaire après l'ajout
        } catch (error) {
          toast.error("Erreur lors de l'ajout de l'événement : " + error.message);
        }
      } else {
        toast.error("Veuillez remplir tous les champs requis.");
      }
    };

    // Réinitialiser le formulaire
    const resetForm = () => {
      event.value = {
        title: "", // Réinitialiser le titre
        event_date: "",
        description: "",
        location: "",
        contact_email: "", // Réinitialiser l'email de contact
        capacity: null, // Réinitialiser la capacité
      };
    };

    return {
      event,
      valid,
      addEvent,
      resetForm,
    };
  },
};
</script>


<style scoped>
.v-container {
  max-width: 600px;
  margin: 0 auto;
}
</style>
