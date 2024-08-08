import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';

@Component({
  selector: 'app-update-admin',
  templateUrl: './update-admin.component.html',
  styleUrl: './update-admin.component.scss'
})
export class UpdateAdminComponent {
  admin = {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    phoneNumber: '',
    roles: ['ADMIN']
  };

  constructor(
    public dialogRef: MatDialogRef<UpdateAdminComponent>,
      @Inject(MAT_DIALOG_DATA) public data: { adminId: number },
      private apiService: ApiService,
      private refreshService: RefreshService
    ) {
      console.log('Dialog data:', data);
    }

  ngOnInit(): void {
    if (this.data.adminId !== undefined && this.data.adminId !== null) {
        console.log(this.data.adminId);
        this.loadAdminData();
      } else {
        console.error('clientId is undefined or null');
      }
  }

  loadAdminData(): void {
    this.apiService.getAdminById(this.data.adminId).subscribe(
      response => {
        console.log('onsode apiService arrow fun', this.data.adminId);
        console.log('Loaded partner data:', response); // Debugging log
        this.admin = response;
      },
      error => {
        console.error('Error loading partner data:', error);
      }
    );
  }

  onUpdate(): void {
    this.apiService.updateAdmin(this.data.adminId, this.admin).subscribe(
      response => {
        this.dialogRef.close(true);
        this.refreshService.triggerRefresh('/users/admins'); // Notify other components
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
