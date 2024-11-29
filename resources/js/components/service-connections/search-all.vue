<template>
    <div class="row">
        <div class="col-lg-12">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-md-6 offset-md-3">
                            <input type="text" class="form-control" placeholder="Search Account # or Account Name" v-model="search" @keyup="searchApplications" autofocus>
                        </div>
                        <div class="col-md-3">
                            <button @click="searchApplications" class="btn btn-success"><i class="fas fa-search ico-tab-mini"></i>Search</button>
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
                        <th>ID | Date Applied</th>
                        <th>Applicant | Address</th>
                        <th>Application | Status</th>
                        <th>Office | Stats</th>
                        <th>OR Details</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="res in resultData" :key="res.ConsumerId" style="cursor: pointer;">
                        <td class="v-align" @click="view(res.ConsumerId)">
                            <span>
                                <strong>{{ res.ConsumerId }}</strong>
                                <span class="ico-tab-left-mini badge" :class="res.Office === 'MAIN OFFICE' ? 'bg-success' : 'bg-danger'">{{ res.Office  }}</span>
                            </span>
                            <br>
                            <span title="Date Applied" class="text-muted text-sm"><i class="fas fa-clock"></i> {{ moment(res.DateOfApplication).format('MMM DD, YYYY (ddd)') }}</span>
                        </td>
                        <td class="v-align" @click="view(res.ConsumerId)">
                            <div style="display: flex; justify-items: start; align-items: center;">
                                <img :src="imgURL + '/prof-icon.png'" style="width: 40px; height: 40px; margin-right: 15px; " class="img-circle" alt="profile">
                                <span>
                                    <strong style="font-size: 1.12em;">
                                        {{ res.ServiceAccountName }}
                                    </strong>
                                    <br>
                                    <span class="text-muted text-sm"><i class="fas fa-map-marker-alt ico-tab-mini"></i>{{ res.Barangay + ', ' + res.Town }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="v-align" @click="view(res.ConsumerId)">
                            <strong>{{ res.ConnectionApplicationType }}</strong>
                            <br>
                            <span class="badge bg-primary">{{ res.Status  }}</span>
                        </td>
                        <td class="v-align" @click="view(res.ConsumerId)">
                            <strong>{{ res.AccountType }}</strong>
                            <br>
                            <span class="text-muted text-sm ico-tab-mini">
                                <i class="fas fa-bolt ico-tab-mini"></i>{{ res.LoadCategory }} kVA
                            </span>
                            <span class="text-muted ico-tab-mini"> • </span>
                            <span class="text-muted text-sm">
                                <i class="fas fa-lightbulb ico-tab-mini"></i>{{ res.AccountApplicationType }}
                            </span>
                        </td>
                        <td class="v-align">
                            <button v-if="isNull(res.ORNumber) ? true : false" class="btn btn-sm btn-default" @click="updateOR(res.ConsumerId)"><i class="fas fa-dollar-sign ico-tab-mini"></i>Update OR</button>
                            <div v-if="isNull(res.ORNumber) ? false : true">
                                <span class="text-muted">Number: </span> <strong>{{ res.ORNumber }}</strong>
                                <br>
                                <span class="text-muted text-sm">Date: {{ isNull(res.ORDate) ? '-' : moment(res.ORDate).format('MMM DD, YYYY') }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <pagination :data="results" :limit="15" @pagination-change-page="searchApplications"></pagination>
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
    name : 'SearchAll.search-all',
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
        isNull (item) {
            if (jquery.isEmptyObject(item)) {
                return true;
            } else {
                return false;
            }
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
        searchApplications(page = 1) {
            axios.get(`${ this.baseURL }/service_connections/search-ajax`, {
                params: {
                    params : this.search,
                    page : page
                }
            }).then(response => {
                this.results = response.data
                this.resultData = this.results.data
            })
            .catch(error => {
                Swal.fire({
                    icon : 'error',
                    title : 'Error getting service connections!',
                });
                console.log(error)
            });
        },
        view(id) {
            window.location.href = this.baseURL + '/serviceConnections/' + id
        },
        updateOR(id) {
            Swal.fire({
                title: 'Update OR Details',
                html:
                    `
                    <input id="or-number" class="form-control" type="text" placeholder="Enter OR Number...">
                    <input id="or-date" class="form-control mt-2" type="date" placeholder="Enter OR Date..." value='${ moment().format('YYYY-MM-DD') }'>
                    `,
                focusConfirm: false,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    const orNumber = document.getElementById('or-number').value;
                    const orDate = document.getElementById('or-date').value;

                    if (!orNumber || !orDate) {
                        Swal.showValidationMessage('Both OR Number and OR Date are required.');
                        return false;
                    }

                    return fetch(this.baseURL + '/service_connections/update-or', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            id : id,
                            ORNumber : orNumber,
                            ORDate : orDate
                        })
                    })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(({ status, body }) => {
                        if (status !== 200) {
                            console.log('Server response:', body);
                            Swal.showValidationMessage(`Request failed: ${body.message || 'Unknown error'}`);
                            return false;
                        }
                        return body
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                        console.log(error.message)
                    })
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.resultData.map(obj => {
                        if (obj.id === id) {
                            return { ...obj, ORNumber: result.value.ORNumber, ORDate : result.value.ORDate }; // Update the name property
                        } else {
                            return obj;
                        }
                    })

                    this.toast.fire({
                        icon : 'success',
                        text : 'OR Updated!'
                    })

                    location.reload()
                }
            })
        }
    },
    created() {
        
    },
    mounted() {
        this.searchApplications()
    }
}

</script>