<template>
    <v-container
            fluid
            fill-height
    >
        <v-layout
                align-center
                justify-center
        >
            <v-flex
                    xs12
                    sm8
                    md4
            >
                <v-card class="elevation-12">
                    <v-toolbar
                            color="primary"
                            dark
                            flat
                    >
                        <v-toolbar-title>Add Work</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-toolbar-title>@TXD</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <form method="post" action="/add">
                            <v-text-field
                                    v-model="name"
                                    :error-messages="nameErrors"
                                    :counter="20"
                                    label="Work name"
                                    required
                                    @input="$v.name.$touch()"
                                    @blur="$v.name.$touch()"
                            ></v-text-field>
                            <v-dialog
                                    ref="dialog"
                                    v-model="modalRange"
                                    :return-value.sync="dates"
                                    persistent
                                    width="290px"
                            >
                                <template v-slot:activator="{ on }">
                                    <v-text-field
                                            v-model="dateRangeText" label="Date range" readonly v-on="on"
                                            :error-messages="datesErrors"
                                            @input="$v.dates.$touch()"
                                            @blur="$v.dates.$touch()"
                                    ></v-text-field>
                                </template>
                                <v-date-picker v-model="dates" range>
                                    <v-spacer></v-spacer>
                                    <v-btn text color="primary" @click="modalRange = false">Cancel</v-btn>
                                    <v-btn text color="primary" @click="$refs.dialog.save(dates)">OK</v-btn>
                                </v-date-picker>
                            </v-dialog>
                            <v-btn class="mr-4" color="primary" @click="submit">submit</v-btn>
                            <v-btn @click="clear">clear</v-btn>
                        </form>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import {validationMixin} from 'vuelidate';
    import {required, maxLength, date} from 'vuelidate/lib/validators';
    import axios from 'axios';

    export default {
        name: 'formWork',
        mixins: [validationMixin],

        validations: {
            name: {required, maxLength: maxLength(20)},
            dates: {required},
        },

        data: () => ({
            dates: [],
            name: '',
            modalRange: false,
        }),

        computed: {
            dateRangeText () {
                return this.dates.join(' ~ ')
            },
            nameErrors() {
                const errors = []
                if (!this.$v.name.$dirty) return errors
                !this.$v.name.maxLength && errors.push('Name must be at most 10 characters long')
                !this.$v.name.required && errors.push('Name is required.')
                return errors
            },
            datesErrors() {
                const errors = []
                if (!this.$v.dates.$dirty) return errors
                !this.$v.dates.required && errors.push('Date range is required.')
                return errors
            },
        },

        methods: {
            submit() {
                this.$v.$touch()
                if (this.$v.$pending || this.$v.$error) return;
                //Call api
                this.$toasted.show("Toasted !!", {
                    theme: "bubble",
                    position: "top-right",
                    duration : 5000
                });
                axios.post('/add', {
                    name: this.name,
                    dates: this.dates
                }).then((res) => {
                    console.log(res)
                }).catch(err=> {
                    console.log(err)
                })
            },
            clear() {
                this.$v.$reset()
                this.name = ''
                this.dates = []
            },
        },
    }
</script>

<style scoped>

</style>