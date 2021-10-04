class Batch {
    constructor(batchNo, expiryDate, quantityAvailable, 
    vaccine, healthcareCenter) {
        this.batchNo = batchNo;
        this.expiryDate = expiryDate;
        this.quantityAvailable = quantityAvailable;
        this.quantityAdministered = 0;
        this.vaccine = vaccine;
        this.healthcareCenter = healthcareCenter;
        this.vaccinations = [];
    }
}
