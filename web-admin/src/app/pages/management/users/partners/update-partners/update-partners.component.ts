import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-update-partners',
  templateUrl: './update-partners.component.html',
  styleUrl: './update-partners.component.scss'
})
export class UpdatePartnersComponent {
  partner = {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    phoneNumber: '',
    companyName: '',
    companyDescription: '',
    localisation: '',
    adminId: null,
    roles: ['PARTNER'],
    imageFilename: null
  };
  file: any;

  constructor(
    public dialogRef: MatDialogRef<UpdatePartnersComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { partnerId: number },
      private apiService: ApiService,
      private refreshService: RefreshService,
    ) {
      console.log('Dialog data:', data);
    }

  ngOnInit(): void {
    if (this.data.partnerId !== undefined && this.data.partnerId !== null) {
        console.log(this.data.partnerId);
        this.loadPartnerData();
      } else {
        console.error('partnerId is undefined or null');
      }
  }

  selectImage(event: any) {
    if (event.target.files.length > 0) {
      const file = event.target.files[0];
      this.partner.imageFilename = file;
    }
  }

  loadPartnerData(): void {
    this.apiService.getPartnerById(this.data.partnerId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.partnerId);
        console.log('Loaded partner data:', response); // Debugging log
        this.partner = response;
      },
      error => {
        console.error('Error loading partner data:', error);
      }
    );
  }
  


  onUpdate(): void {
    this.apiService.updatePartner(this.data.partnerId, this.partner).subscribe(
      response => {
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/users/partners'); // Notify other components
      },
      error => {
        console.error('Error updating partner:', error);
        // Optionally show an error message to the user
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close(); // Close the modal without any action
  }

}