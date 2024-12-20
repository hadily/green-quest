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
    roles: ['PARTNER'],
    imageFilename: null,
  };
  admins : any[] = [];
  file: any;

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

  // handleFile(e: any){
  //   this.file = e.target.value
  // }

  selectImage(event: any) {
    this.partner.imageFilename = event.target.files[0];
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
    console.log(this.partner)
    this.partner['password'] = this.generateRandomPassword();
    console.log('pwd : ',this.partner['password']);
    this.apiService.createPartner(this.partner).subscribe(
      response => {
        console.log('Partner created:', response);

        this.apiService.sendEmail(this.partner.email, this.partner['password']).subscribe(
          emailResponse => {
            console.log('Welcome email sent:', emailResponse);
          },
          emailError => {
            console.error('Error sending welcome email:', emailError);
          }
        );

        this.refreshService.triggerRefresh('/users/partners'); // Emit a value to notify other components
        this.closeModal();
      },
      error => {
        console.error('Error creating partner:', error);
      }
    );
  }

  closeModal(): void {
    this.dialogRef.close();
  }



}
