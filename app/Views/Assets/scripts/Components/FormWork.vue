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
                                    :counter="50"
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
                            <v-select
                                    v-model="status"
                                    :items="statusItem"
                                    :error-messages="statusErrors"
                                    label="Status"
                                    required
                                    @change="$v.status.$touch()"
                                    @blur="$v.status.$touch()"
                            ></v-select>
                            <v-btn class="mr-4" color="primary" @click="submit" :disabled="disabled">submit</v-btn>
                            <v-btn @click="clear" class="mr-4">clear</v-btn>
                            <v-btn href="/">Back to home</v-btn>
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
            name: {required, maxLength: maxLength(50)},
            dates: {required},
            status: {required},
        },

        data: () => ({
            dates: [],
            name: '',
            status,
            statusItem: [
                'Planing',
                'Doing',
                'Complete'
            ],
            disabled: false,
            modalRange: false,
        }),

        computed: {
            dateRangeText() {
                return this.dates.join(' ~ ')
            },
            nameErrors() {
                const errors = []
                if (!this.$v.name.$dirty) return errors
                !this.$v.name.maxLength && errors.push('Name must be at most 50 characters long')
                !this.$v.name.required && errors.push('Name is required.')
                return errors
            },
            datesErrors() {
                const errors = []
                if (!this.$v.dates.$dirty) return errors
                !this.$v.dates.required && errors.push('Date range is required.')
                return errors
            },
            statusErrors() {
                const errors = []
                if (!this.$v.status.$dirty) return errors
                !this.$v.status.required && errors.push('Status is required.')
                return errors
            },
        },

        methods: {
            submit() {
                this.$v.$touch()
                if (this.$v.$pending || this.$v.$error) return;
                this.disabled = true;
                let start = this.dates[0];
                let end = this.dates[0];
                if (typeof this.dates[1] !== 'undefined') {
                    end = this.dates[1];
                }

                let status = 0;
                switch (this.status) {
                    case 'Doing':
                        status = 1;
                        break;
                    case 'Complete':
                        status = 2;
                        break;
                    default:
                        status = 0;
                        break;
                }

                axios.post('/add', {
                    name: this.name,
                    start,
                    end,
                    status
                }).then((res) => {
                    this.clear();
                    if (res.data.success) {
                        this.$toasted.info("Add Work successfully! Redirecting to home page in 2 seconds", {
                            theme: "bubble",
                            position: "top-right",
                            duration: 5000
                        });
                        setTimeout(function () {
                            window.location = '/';
                        }, 2000);
                    } else {
                        this.$toasted.error("Some thing went wrong!", {
                            theme: "bubble",
                            position: "top-right",
                            duration: 5000
                        });
                    }
                }).catch(() => {
                    //Call api
                    this.$toasted.error("Some thing went wrong!", {
                        theme: "bubble",
                        position: "top-right",
                        duration: 5000
                    });
                })
            },
            clear() {
                this.$v.$reset();
                this.name = '';
                this.dates = [];
                this.status = '';
            },
        },
    }
</script>

<style scoped>

</style>