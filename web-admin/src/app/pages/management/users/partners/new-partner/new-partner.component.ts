import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { HttpClient } from '@angular/common/http';
import { ApiService } from 'src/app/services/api.service';
import { RefreshService } from 'src/app/services/refresh.service';
import { tap } from 'rxjs/operators';


@Component({
  selector: 'app-new-partner',
  templateUrl: './new-partner.component.html',
  styleUrl: './new-partner.component.scss'
})
export class NewPartnerComponent implements OnInit {
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
    roles: ['PARTNER']
  };
  admins : any[] = [];

  constructor(
    public dialogRef: MatDialogRef<NewPartnerComponent>,
    private http: HttpClient,
    private apiService: ApiService,
    private refreshService: RefreshService
  ) {}

  ngOnInit(): void {
    this.loadAdmins();
  }

  loadAdmins(): void {
    this.apiService.getAllAdmins().subscribe(
      data => this.admins = data,
      error => console.error('Error fetching admins:', error)
    );
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
    this.partner['password'] = this.generateRandomPassword();
    this.apiService.createPartner(this.partner).subscribe(
      response => {
        console.log('Partner created:', response);
        this.refreshService.triggerRefresh('/users/partners'); // Emit a value to notify other components
        this.closeModal();
      },
      error => {
        console.error('Error creating partner:', error);
        // Optionally show an error message to the user
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close();
  }

}
