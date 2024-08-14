import { Component, ViewChild, OnInit } from '@angular/core';
import { ModalConfig, ModalComponent } from '../../_metronic/partials';
import { ApiService } from '../../services/api.service';
import { AuthService } from 'src/app/modules/auth';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'],
})
export class DashboardComponent implements OnInit {
  modalConfig: ModalConfig = {
    modalTitle: 'Modal title',
    dismissButtonLabel: 'Submit',
    closeButtonLabel: 'Cancel'
  };
  @ViewChild('modal') private modalComponent: ModalComponent;

  async openModal() {
    return await this.modalComponent.open();
  }

  user: any;

  constructor(
    private apiService: ApiService,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.authService.getUserByToken().subscribe(
      data => {
        this.user = data;
        console.log('userId ', this.user.id);
        this.loadUserData(this.user.id);
      },
      error => {
        console.error('Error fetching users:', error); // Log any errors
      }
    );
  }

  loadUserData(userId: number): void {
    this.apiService.getUserById(userId).subscribe(
      data => {
        this.user = data;
      },
      error => {
        console.error('Error fetching users:', error); // Log any errors
      }
    );
  }
}
