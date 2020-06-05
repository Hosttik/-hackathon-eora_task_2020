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
    name: 'Reg',
    data() {
      return {
        valid: false,
        email: '',
        emailRules: [v => !!v || 'Email обязательное поле', v => /.+@.+/.test(v) || 'Не валидный email',],
        password: '',
        passwordRules: [v => !!v || 'Пароль обязательное поле'],
        first_name: '',
        firstNameRules: [v => !!v || 'Имя обязательно']
      }
    },
    methods: {
      ...mapMutations(['setIsAuthorized', 'setUserData']),
      getData: async function () {
        const res = await apiHost.post('/site-api/get-user-info');
        if (res.is_success) {
          if (res.content.is_guest) {
          } else {
            this.setUserData(res.content);
          }
        } else {
        }
      },
      register: async function () {
        try {
          const res = await apiHost.post('/site-api/user-register', qs.stringify({
            email: this.email,
            password: this.password,
            first_name: this.first_name
          }));
          if (res.is_success) {
            showNotify({
              text: 'Успешно зарегистрированы',
              type: 'success'
            });
            await this.getData();
            this.setIsAuthorized(true);
            if (this.$store.state.currentAdvert) {
              this.$router.push({path: `/advert/${this.$store.state.currentAdvert}`});
            } else {
              this.$router.push({path: 'user-main'});
            }
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
