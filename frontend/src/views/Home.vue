<template>
  <v-layout>
    <v-container fluid>
      <v-layout row wrap>
        <v-flex xs6 offset-xs3>
          <v-card class="mb-5 pa40" color="green lighten-4">
                <h2>
                    Create a date with the Queen
                </h2>
                <v-form ref="form" v-model="form.valid" lazy-validation >
                    <v-text-field
                        prepend-icon="person" 
                        v-model="form.name" 
                        :counter="10" 
                        :rules="nameRules" 
                        label="Nombre" required>
                    </v-text-field>
                    <br>
                    <v-btn :loading="loading" :disabled="!form.valid" color="success" class="mr-4" @click="validate">
                        Create
                    </v-btn>
                </v-form>
            </v-card>
        </v-flex>
      </v-layout>
    </v-container>
  </v-layout>
</template>

<script>
  import axios from "axios";

  export default {
    data () {
      return {
        configs: this.$configs,
        viewName: this.$route.meta.title,
        loader: null,
        loading: false,
        form: {
          valid: true,
          clientEmail: 'hola@fortalece-tu-empresa.com',
          name: null,
          phone: null,
          message: null,
          email: null,
        },
        nameRules: [
          v => !!v || 'Nombre Requerido',
          v => (v && v.length <= 10) || 'El nombre no debe tener más de 10 caracteres.',
        ],
        emailRules: [
          v => !!v || 'Correo electrónico Requerido',
          v => /.+@.+\..+/.test(v) || 'Formato de correo invalido',
          v => (v && v.length <= 40) || 'El correo no debe tener más de 40 caracteres.',
        ],
        phoneRules: [
          v => !!v || 'Télefono Requerido',
          v => (v && v.length <= 30) || 'El Télefono no debe tener menos de 30 caracteres',
        ],
      }
    },
    watch: {
      loader () {
        const l = this.loader
        this[l] = !this[l]
      },
    },
    methods: {
      validate() {
        let vm = this;

        if (vm.$refs.form.validate()) {
            vm.loading = true;
            vm.sendContactForm();
        }
      },
      sendContactForm(){
        let vm = this;

        axios.post(vm.configs.apiUrl, vm.form)
        .then(function (response) {
          console.log(response);
          
        })
        .catch(function (error) {
          console.log(error);
          
        });
      },
      showSnackBar(color, message){
        let vm = this;
        vm.$emit("showSnackBar", {
          color: color,
          snackbar: true,
          timeout: 8000,
          text: message,
        });
      }
    },
  }
</script>

<style lang="scss">
  $padding: 40px;
  $font: 'Raleway', sans-serif;

  .pa40{
      padding: $padding;
  }

  h1, h2, h3, h4, p, ul, li{
    font-family: $font;
  }
</style>
