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
    roles: ['CLIENT'],
    imageFilename: null
  };
  file: any;

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
      } else {
        console.error('clientId is undefined or null');
      }
  }
  
  selectImage(event: any) {
    if (event.target.files.length > 0) {
      const file = event.target.files[0];
      this.client.imageFilename = file;
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

  onUpdate(): void {
    this.file = this.client.imageFilename;
    this.apiService.updateClient(this.data.clientId, this.client, this.file)
      .subscribe(response => {
        console.log('Client updated successfully', response);
        this.refreshService.triggerRefresh('/users/clients'); // Emit a value to notify other components
        this.closeModal();
      }, error => {
        console.error('Error updating client', error);
      });
  }

  closeModal(): void {
    this.dialogRef.close(); // Close the modal without any action
  }

}
