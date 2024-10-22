<template>
    <div class="row">
        <div class="col-lg-12">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-md-8 offset-md-2 mb-2">
                            <span class="text-muted text-sm">Advanced Search</span>
                        </div>
                        <div class="col-md-4 offset-md-2">
                            <input type="text" class="form-control" placeholder="Search Ticket ID, Account Number, Account Name, or Meter Number..." v-model="search" @keyup="searchTickets" autofocus>
                        </div>
                        <div class="col-md-4">
                            <div style="display: flex; flex-direction: row; column-gap: 5px;">
                                <!-- ticket type -->
                                <select class="custom-select select2" v-model="ticketType" style="width: 240px;" @change="searchTickets">
                                    <option value="All">All</option>
                                    <optgroup v-for="pt in ticketTypes" :label="pt.Name">
                                        <option v-for="tt in pt.Tickets" :value="tt.id" :key="tt.id">{{ tt.Name }}</option>
                                    </optgroup>
                                    
                                </select>
                                <!-- towns -->
                                <select class="form-control" v-model="town" style="width: 160px;" @change="searchTickets">
                                    <option value="All">All</option>
                                    <option v-for="t in towns" :value="t.id" :key="t.id">{{ t.Town }}</option>
                                </select>
                                <button @click="searchTickets" class="btn btn-success"><i class="fas fa-search ico-tab-mini"></i>Search</button>
                            </div>
                        </div>
                        <!-- <div class="col-md-8 offset-md-2">
                            <p class="text-muted text-sm no-pads">Powered by <strong>hashed.IT</strong></p>
                        </div> -->
                    </div>
                </div>
            </section>
        </div>

        <!-- results -->
        <div class="col-lg-12 table-responsive px-4">
            <table class="table table-hover table-sm">
                <thead>
                    <th>Ticket ID</th>
                    <th>Consumer Name<br>Account No.</th>
                    <th>Ticket</th>
                    <th>Address</th>
                    <th>Meter No</th>
                    <th>Status</th>
                    <th>Office</th>
                    <th style="width: 150px;"></th>
                </thead>
                <tbody>
                    <tr v-for="res in resultData">
                        <td class="v-align">
                            <strong><a :href="baseURL + '/tickets/' + res.id">{{ res.id }}</a></strong>
                            <p class="no-pads text-muted text-sm">{{ moment(res.created_at).format("ddd, MMM DD, YYYY hh:mm A") }}</p>
                        </td>
                        <td @click="view(res.id)" class="v-align pointer">
                            <strong>{{ res.ConsumerName }}</strong>
                            <br>
                            <span class="text-muted text-sm">{{ res.AccountNumber }}</span>
                        </td>
                        <td @click="view(res.id)" class="v-align pointer">
                            <strong>{{ res.Ticket }}</strong>
                            <p class="no-pads text-muted">{{ res.ParentTicket }}</p>
                        </td>
                        <td @click="view(res.id)" class="v-align pointer">{{ (isNull(res.Sitio) ? '' : res.Sitio + ', ') + (isNull(res.Barangay) ? '' : (res.Barangay + ',')) }} <strong>{{ (isNull(res.Town) ? '' : (res.Town)) }}</strong></td>
                        <td @click="view(res.id)" class="v-align pointer">{{ res.CurrentMeterNo }}</td>
                        <td @click="view(res.id)" class="v-align pointer"><span class="badge bg-info">{{ res.Status }}</span></td>
                        <td @click="view(res.id)" class="v-align pointer"><span class="badge" :class="res.Office === 'MAIN OFFICE' ? 'bg-primary' : 'bg-warning'">{{ res.Office }}</span></td>
                        <td class="v-align text-right">
                            <a class="btn btn-sm btn-link-muted" :href="baseURL + `/tickets/print-ticket/${ res.id }`" title="Print ticket"><i class="fas fa-print"></i></a>
                            <a class="btn btn-sm btn-link-muted" :href="baseURL + `/tickets/${ res.id }/edit`" title="Edit ticket details"><i class="fas fa-pen"></i></a>
                            <button class="btn btn-sm btn-link-muted" @click="removeTicket(res.id)" title="Delete ticket"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <pagination :data="results" :limit="15" @pagination-change-page="searchTickets"></pagination>
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
            towns : [],
            town : 'All',
            ticketTypes : [],
            ticketType : 'All'
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
        searchTickets(page = 1) {
            axios.get(`${ this.baseURL }/tickets/search-tickets`, {
                params: {
                    search : this.search,
                    page : page,
                    Town : this.town,
                    TicketType : this.ticketType,
                }
            }).then(response => {
                this.results = response.data
                this.resultData = this.results.data
            })
            .catch(error => {
                console.log(error.response)
            });
        },
        view(id) {
            window.location.href = this.baseURL + '/tickets/' + id
        },
        removeTicket(id) {
            Swal.fire({
                title: "Confirm Delete",
                text : `Deleting this ticket data will be moved to the Trash section. You can always restore this anytime.`,
                showCancelButton: true, 
                confirmButtonText: "Proceed Delete",
                confirmButtonColor : '#e03822'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`${ this.baseURL }/tickets/delete-one`, {
                        _token : this.token,
                        id : id,
                    })
                    .then(response => {
                        this.toast.fire({
                            icon : 'success',
                            text : 'Ticket data removed!'
                        })
                        
                        this.resultData = this.resultData.filter(obj => obj.id !== id)
                    })
                    .catch(error => {
                        console.log(error.response)
                        this.toast.fire({
                            icon : 'error',
                            text : 'Error removing ticket data!\n' + error.response
                        })
                    })
                }
            })
        },
        getTowns() {
            axios.get(`${ this.baseURL }/towns/get-towns`)
            .then(response => {
                this.towns = response.data
            })
            .catch(error => {
                this.toast.fire({
                    icon : 'error',
                    title : 'Error getting towns!',
                });
                console.log(error.response)
            });
        },
        getTicketTypes() {
            axios.get(`${ this.baseURL }/tickets/get-ticket-types`)
            .then(response => {
                this.ticketTypes = response.data
                console.log(this.ticketTypes)
            })
            .catch(error => {
                this.toast.fire({
                    icon : 'error',
                    title : 'Error getting ticket types!',
                });
                console.log(error.response)
            });
        }
    },
    created() {
        
    },
    mounted() {
        this.searchTickets()
        this.getTowns()
        this.getTicketTypes()
    }
}

</script>

