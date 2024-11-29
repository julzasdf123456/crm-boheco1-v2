<template>
    <div class="row">
        <div class="col-lg-12">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-md-6 offset-md-3">
                            <input type="text" class="form-control" placeholder="Search Account Name, Account Number, Meter Number..." v-model="search" @keyup="searchAccounts" autofocus>
                        </div>
                        <div class="col-md-3">
                            <button @click="searchAccounts" class="btn btn-success"><i class="fas fa-search ico-tab-mini"></i>Search</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- results -->
        <div class="col-lg-12 table-responsive px-4">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Consumer Name</th>
                        <th>Address</th>
                        <th>Meter Number</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="res in resultData">
                        <td class="v-align">
                            <strong><a :href="baseURL + '/account_masters/view-account/' + res.AccountNumber">{{ res.AccountNumber }}</a></strong>
                            <br>
                            <span class="badge" :class="res.AccountStatus === 'ACTIVE' ? 'bg-success' : 'bg-danger'">{{ res.AccountStatus }}</span>
                        </td>
                        <td @click="view(res.AccountNumber)" class="v-align pointer">
                            <strong>{{ res.ConsumerName }}</strong>
                            <br>
                            <p class="text-muted text-sm no-pads">{{ res.ConsumerType }}</p>
                        </td>
                        <td @click="view(res.AccountNumber)" class="v-align pointer">{{ res.ConsumerAddress }}</td>
                        <td @click="view(res.AccountNumber)" class="v-align pointer">{{ res.MeterNumber }}</td>
                    </tr>
                </tbody>
            </table>
            <pagination :data="results" :limit="15" @pagination-change-page="searchAccounts"></pagination>
        </div>
    </div>

</template>

<script>
import axios from 'axios';
import moment from 'moment';
import FlatPickr from 'vue-flatpickr-component';
import { Bootstrap4Pagination } from 'laravel-vue-pagination'
import 'flatpickr/dist/flatpickr.css';
import jquery from 'jquery';
import Swal from 'sweetalert2';
import { renderSlot } from 'vue';

export default {
    components : {
        FlatPickr,
        Swal,
        'pagination' : Bootstrap4Pagination
    },
    data() {
        return {
            moment : moment,
            baseURL : window.location.origin + axios.defaults.baseURL,
            imgURL : window.location.origin + axios.defaults.imgURL,
            colorProfile : document.querySelector("meta[name='color-profile']").getAttribute('content'),
            tableInputTextColor : this.isNull(document.querySelector("meta[name='color-profile']").getAttribute('content')) ? 'text-dark' : 'text-white',
            pickerOptions: {
                enableTime: false, // Enable time selection
                dateFormat: 'Y-m-d', // Date format
            },
            toast : Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            }),
            search : '',
            results :  {},
            resultData : [],
        }
    },
    methods : {
        isNull (value) {
            // Check for null or undefined
            if (value === null || value === undefined) {
                return true;
            }

            // Check for empty string
            if (typeof value === 'string' && value.trim() === '') {
                return true;
            }

            // Check for empty array
            if (Array.isArray(value) && value.length === 0) {
                return true;
            }

            // Check for empty object
            if (typeof value === 'object' && !Array.isArray(value) && Object.keys(value).length === 0) {
                return true;
            }

            // Check for NaN
            if (typeof value === 'number' && isNaN(value)) {
                return true;
            }

            // If none of the above, it's not null, empty, or undefined
            return false;
        },
        toMoney(value) {
            if (this.isNumber(value)) {
                return Number(parseFloat(value).toFixed(2)).toLocaleString("en-US", { maximumFractionDigits: 2, minimumFractionDigits: 2 })
            } else {
                return '-'
            }
        },
        isNumber(value) {
            return typeof value === 'number';
        },        
        round(value) {
            return Math.round((value + Number.EPSILON) * 100) / 100;
        },
        generateRandomString(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                result += characters.charAt(randomIndex);
            }

            return result;
        },
        generateUniqueId() {
            return moment().valueOf() + "-" + this.generateRandomString(32);
        },
        searchAccounts(page = 1) {
            axios.get(`${ this.baseURL }/account_masters/get-search`, {
                params: {
                    search : this.search,
                    page : page
                }
            }).then(response => {
                this.results = response.data
                this.resultData = this.results.data
            })
            .catch(error => {
                // this.toast.fire({
                //     icon : 'error',
                //     title : 'Error getting membership data!',
                // });
                console.log(error.response)
            });
        },
        view(id) {
            window.location.href = this.baseURL + '/account_masters/view-account/' + id
        },
    },
    created() {
        
    },
    mounted() {
        this.searchAccounts()
    }
}

</script>