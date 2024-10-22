<template>
    <div class="row">
        <div class="col-lg-12">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-md-6 offset-md-3">
                            <input type="text" class="form-control" placeholder="Search ID or Membership Name..." v-model="search" @keyup="searchMembership" autofocus>
                        </div>
                        <div class="col-md-3">
                            <button @click="searchMembership" class="btn btn-success"><i class="fas fa-search ico-tab-mini"></i>Search</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- results -->
        <div class="col-lg-12 table-responsive px-4">
            <table class="table table-hover table-sm">
                <thead>
                    <th>Membership ID</th>
                    <th>Full Name</th>
                    <th>Address</th>
                    <th>Contact No.</th>
                    <th>Membership Type</th>
                    <th>Office</th>
                    <th></th>
                </thead>
                <tbody>
                    <tr v-for="res in resultData">
                        <td class="v-align"><strong><a :href="baseURL + '/memberConsumers/' + res.ConsumerId">{{ res.ConsumerId }}</a></strong></td>
                        <td @click="createNew(res.ConsumerId)" class="v-align pointer">
                            <div style="display: flex; justify-items: start; align-items: center;">
                                <img :src="imgURL + '/prof-icon.png'" style="width: 40px; height: 40px; margin-right: 15px; " class="img-circle" alt="profile">
                                <span>
                                    <strong style="font-size: 1.12em;">
                                        {{ getConsumerName(res) }}
                                    </strong>
                                </span>
                            </div>
                        </td>
                        <td @click="createNew(res.ConsumerId)" class="v-align pointer">{{ (isNull(res.Sitio) ? '' : res.Sitio + ', ') + (isNull(res.Barangay) ? '' : (res.Barangay + ',')) }} <strong>{{ (isNull(res.Town) ? '' : (res.Town)) }}</strong></td>
                        <td @click="createNew(res.ConsumerId)" class="v-align pointer">{{ res.ContactNumbers }}</td>
                        <td @click="createNew(res.ConsumerId)" class="v-align pointer">{{ res.Type }}</td>
                        <td @click="createNew(res.ConsumerId)" class="v-align pointer"><span class="badge" :class="res.Office === 'MAIN OFFICE' ? 'bg-primary' : 'bg-warning'">{{ res.Office }}</span></td>
                        <td class="v-align text-right">
                            <button class="btn btn-primary" @click="createNew(res.ConsumerId)">Go <i class="fas fa-arrow-right"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <pagination :data="results" :limit="15" @pagination-change-page="searchMembership"></pagination>
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
        searchMembership(page = 1) {
            axios.get(`${ this.baseURL }/member_consumers/get-search`, {
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
        createNew(id) {
            window.location.href = this.baseURL + '/service_connections/create_new/' + id
        },
        getConsumerName(data) {
            if (this.isNull(data)) {
                return '-'
            } else {
                if (this.isNull(data.Type)) {
                    return data.FirstName + ' ' + data.LastName
                } else {
                    if (data.Type === 'Juridical') {
                        if (this.isNull(data.OrganizationName)) {
                            return data.FirstName + ' ' + data.LastName
                        } else {
                            return data.OrganizationName
                        }
                    } else {
                        return data.LastName + ', ' + data.FirstName + (this.isNull(data.SpouseFirstName) ? '' : ('/' + data.SpouseFirstName))
                    }
                }
            }
        }
    },
    created() {
        
    },
    mounted() {
        this.searchMembership()
    }
}

</script>