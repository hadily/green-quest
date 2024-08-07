import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-update-client',
  templateUrl: './update-client.component.html',
  styleUrl: './update-client.component.scss'
})
export class UpdateClientComponent {
  client = {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    phoneNumber: '',
    localisation: '',
    adminId: null,
    roles: ['CLIENT']
  };
  admins: any[] = [];

  constructor(
    public dialogRef: MatDialogRef<UpdateClientComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { clientId: number },
      private apiService: ApiService,
      private refreshService: RefreshService
    ) {
      console.log('Dialog data:', data);
    }

  ngOnInit(): void {
    if (this.data.clientId !== undefined && this.data.clientId !== null) {
        console.log(this.data.clientId);
        this.loadClientData();
        this.loadAdmins(); // Load admins to populate the dropdown
      } else {
        console.error('clientId is undefined or null');
      }
  }

  loadClientData(): void {
    this.apiService.getClientById(this.data.clientId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.clientId);
        console.log('Loaded partner data:', response); // Debugging log
        this.client = response;
      },
      error => {
        console.error('Error loading partner data:', error);
      }
    );
  }
  
  loadAdmins(): void {
    this.apiService.getAllAdmins().subscribe(
      response => {
        console.log('Loaded admins:', response); // Debugging log
        this.admins = response;
      },
      error => {
        console.error('Error loading admins:', error);
      }
    );
  }

  onUpdate(): void {
    this.apiService.updateClient(this.data.clientId, this.client).subscribe(
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
