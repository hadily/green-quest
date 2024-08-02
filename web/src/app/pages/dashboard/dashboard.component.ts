import { Component, OnInit, ViewChild } from '@angular/core';
import { ModalConfig, ModalComponent } from '../../_metronic/partials';
import { ApiService } from '../../api.service';


@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'],
})
export class DashboardComponent implements OnInit{
  modalConfig: ModalConfig = {
    modalTitle: 'Modal title',
    dismissButtonLabel: 'Submit',
    closeButtonLabel: 'Cancel'
  };
  @ViewChild('modal') private modalComponent: ModalComponent;
  constructor(private apiService: ApiService) {}

  async openModal() {
    return await this.modalComponent.open();
  }

  users: any[] = [];

  ngOnInit(): void {
    this.apiService.getAllUsers().subscribe(
      data => {
        console.log('Data received in component:', data); // Log in component
        this.users = data;
      },
      error => {
        console.error('Error fetching users:', error); // Log any errors
      }
    );
  }

}
