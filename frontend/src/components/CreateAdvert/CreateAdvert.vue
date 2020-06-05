<template>
    <v-flex>
        <v-row align="center" justify="center" class="md6">
            <v-col cols="4">
                <v-card>
                    <v-form>
                        <v-container>
                            <v-text-field
                                    v-model="title"
                                    :rules="titleRules"
                                    label="Название"
                                    required
                            ></v-text-field>
                            <v-textarea
                                    label="Описание"
                                    v-model="description"
                                    :rules="descriptionRules"
                                    required
                            ></v-textarea>
                            <v-text-field
                                    v-model="cost"
                                    :rules="costRules"
                                    label="Стоимость"
                                    required
                            ></v-text-field>
                            <v-btn class="block" x-large color="secondary" @click="send">Опубликовать</v-btn>
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
    name: 'CreateAdvert',
    data() {
      return {
        title: '',
        titleRules: [v => !!v || 'Название обязательное поле'],
        description: '',
        descriptionRules: [v => !!v || 'Заполните описание'],
        cost: '',
        costRules: [v => !!v || 'Заполните сумму']
      }
    },
    methods: {
      send: async function () {
        try {
          const res = await apiHost.post('/adverts-api/add', qs.stringify({
            title: this.title,
            description: this.description,
            cost: this.cost
          }));
          if (res.is_success) {
            showNotify({
              text: 'Успешно зарегистрированы',
              type: 'success'
            });
            this.$router.push({path: 'specialist-main'});
          } else {
            showNotify({
              text: 'Произошла ошибка',
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
