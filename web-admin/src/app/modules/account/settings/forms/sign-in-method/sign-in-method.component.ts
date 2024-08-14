import { ChangeDetectorRef, Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { BehaviorSubject, Subscription } from 'rxjs';
import { AuthService } from 'src/app/modules/auth';
import { ApiService } from 'src/app/services/api.service';

@Component({
  selector: 'app-sign-in-method',
  templateUrl: './sign-in-method.component.html',
})
export class SignInMethodComponent implements OnInit, OnDestroy {
  showChangePasswordForm: boolean = false;
  isLoading$: BehaviorSubject<boolean> = new BehaviorSubject<boolean>(false);
  isLoading: boolean;
  private unsubscribe: Subscription[] = [];
  passwordForm: FormGroup;

  constructor(
    private cdr: ChangeDetectorRef,
    private apiService: ApiService,
    private authService: AuthService,
    private fb: FormBuilder,
  ) {
    const loadingSubscr = this.isLoading$
      .asObservable()
      .subscribe((res) => (this.isLoading = res));
    this.unsubscribe.push(loadingSubscr);
    
    this.passwordForm = this.fb.group({
      currentPassword: ['', Validators.required],
      newPassword: ['', [Validators.required, Validators.minLength(8)]],
      passwordConfirmation: ['', Validators.required],
    });
  }

  ngOnInit(): void {}

  togglePasswordForm(show: boolean) {
    this.showChangePasswordForm = show;
  }

  savePassword() {
    if (this.passwordForm.invalid) {
      return;
    }
  
    const formData = this.passwordForm.value;
    const userId = this.authService.currentUserValue?.id ?? 0; 

    if (formData.newPassword !== formData.passwordConfirmation) {
      alert('New password and confirmation do not match');
      return;
    }
  
    this.isLoading = true;
  
    this.apiService
      .resetPassword(userId, {
        currentPassword: formData.currentPassword,
        newPassword: formData.newPassword,
      })
      .subscribe(
        (response) => {
          alert('Password updated successfully');
          this.isLoading = false;
          this.showChangePasswordForm = false;
        },
        (error) => {
          console.error('Error updating password:', error);
          alert('Failed to update password');
          this.isLoading = false;
        }
      );
  }
  ngOnDestroy() {
    this.unsubscribe.forEach((sb) => sb.unsubscribe());
  }
}
