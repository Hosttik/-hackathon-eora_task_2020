<template>
    <v-flex>
        <v-row align="center" justify="center" class="md6">
            <v-col cols="4">
                <v-card>
                    <v-form v-model="valid">
                        <v-container>
                            <v-text-field
                                    v-model="email"
                                    :rules="emailRules"
                                    label="Email"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="password"
                                    :rules="passwordRules"
                                    label="Пароль"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="first_name"
                                    :rules="firstNameRules"
                                    label="Имя"
                                    required
                            ></v-text-field>
                            <v-select
                                    v-model="job_name"
                                    :rules="jobNameRules"
                                    label="Выберете профессию"
                                    :items="jobNames"
                                    item-text="title"
                                    item-value="id"
                                    required
                            ></v-select>
                            <v-textarea
                                    label="Описание"
                                    v-model="description"
                                    :rules="descriptionRules"
                                    required
                            ></v-textarea>
                            <v-btn class="block" x-large color="secondary" @click="register">Зарегистрироваться</v-btn>
                        </v-container>
                    </v-form>
                </v-card>
            </v-col>
        </v-row>
    </v-flex>

</template>

<script>
  import { apiHost } from 'src/api/api.utils';
  import showNotify from 'src/helpers/showNotify';
  import { mapMutations } from 'vuex';
  import qs from 'qs'

  export default {
    name: 'SpecialistRegister',
    mounted: async function () {
      const res = await apiHost.get('/site-api/get-job-names');
      if (res.is_success) {
        this.jobNames = res.content.job_names;
      } else {
        showNotify({
          text: 'Произошла ошибка',
          type: 'error'
        })
      }
    },
    data() {
      return {
        valid: false,
        email: '',
        emailRules: [v => !!v || 'Email обязательное поле', v => /.+@.+/.test(v) || 'Не валидный email',],
        password: '',
        passwordRules: [v => !!v || 'Пароль обязательное поле'],
        first_name: '',
        firstNameRules: [v => !!v || 'Имя обязательно'],
        job_name: '',
        jobNames: [],
        jobNameRules: [v => !!v || 'Выберете профессию'],
        description: '',
        descriptionRules: [v => !!v || 'Заполните описание']
      }
    },
    methods: {
      ...mapMutations(['setIsAuthorized']),
      register: async function () {
        try {
          const res = await apiHost.post('/site-api/specialist-register ', qs.stringify({
            email: this.email,
            password: this.password,
            first_name: this.first_name,
            job_name: this.job_name,
            description: this.description
          }));
          if (res.is_success) {
            showNotify({
              text: 'Успешно зарегистрированы',
              type: 'success'
            });
            this.setIsAuthorized(true);
            this.$router.push({path: 'specialist-main'});
          } else {
            showNotify({
              text: res.errors[0],
              type: 'error'
            })
          }
        } catch (e) {
          showNotify({
            text: 'Произошла ошибка',
            type: 'error'
          })
        }
      }
    }
  }
</script>

<style scoped>
    .block {
        display: block;
        margin: 0 auto;
    }

</style>
