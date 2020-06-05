<template>
    <div>
        <v-navigation-drawer
                v-model="drawer"
                expand-on-hover
                light
                absolute
        >
            <v-list
                    dense
                    nav
                    class="py-0"
            >
                <v-list-item two-line :class="miniVariant && 'px-0'">
                    <v-list-item-avatar>
                        <img src="https://randomuser.me/api/portraits/men/81.jpg">
                    </v-list-item-avatar>

                    <v-list-item-content>
                        <v-list-item-title>Имя</v-list-item-title>
                        <v-list-item-subtitle>Специальность</v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item
                        v-for="item in items"
                        :key="item.title"
                        :to="item.href"
                        link
                >
                    <v-list-item-icon>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-icon>

                    <v-list-item-content>
                        <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>

        </v-navigation-drawer>
        <v-flex>
            <v-row align="center" justify="center" class="md6">
                <v-col cols="3">
                    <v-card>
                        <v-card-title class="headline">Выполнено:<br> 0 консультаций</v-card-title>
                        <v-card-subtitle>Средняя оценка 0</v-card-subtitle>
                        <v-card-actions>
                            <v-btn text>Подробнее</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
                <v-col cols="3">
                    <v-card>
                        <v-card-title class="headline">Заработано: 0 рублей</v-card-title>
                        <v-card-actions>
                            <v-btn text>Подробнее</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
            <v-row align="center" justify="center" class="md6">
                <v-col cols="4">
                    <v-card>
                        <v-btn class="block" x-large color="secondary" to="/create-advert">Создать объявление о
                            предоставлении услуги
                        </v-btn>
                    </v-card>
                </v-col>
            </v-row>
        </v-flex>
    </div>

</template>

<script>
  export default {
    name: 'SpecialistMain',
    data() {
      return {
        drawer: true,
        miniVariant: true,
        items: [{
          title: 'Заказы',
          icon: 'fa-bell',
          href: '/orders'
        }, {
          title: 'История заказов',
          icon: 'fa-th-list',
          href: '/orders-history'
        },],
      }
    },
    methods: {

    }
  }
</script>

<style scoped>

</style>
