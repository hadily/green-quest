import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { HttpClient } from '@angular/common/http';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { tap } from 'rxjs/operators';

@Component({
  selector: 'app-new-admin',
  templateUrl: './new-admin.component.html',
  styleUrl: './new-admin.component.scss'
})
export class NewAdminComponent {
  admin= {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    phoneNumber: '',
    roles: ['ADMIN']
  };

  constructor(
    public dialogRef: MatDialogRef<NewAdminComponent>,
    private http: HttpClient,
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
  }

  generateRandomPassword(): string {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let password = '';
    for (let i = 0; i < 10; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
  }
  
  onSubmit(): void {
    this.admin['password'] = this.generateRandomPassword();
    this.apiService.createAdmin(this.admin).subscribe(
      response => {
        console.log('Admin created:', response);
        this.refreshService.triggerRefresh('/users/admins'); // Emit a value to notify other components
        this.closeModal();
      },
      error => {
        console.error('Error creating admin:', error);
        // Optionally show an error message to the user
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close();
  }
}
