// refresh.service.ts
import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class RefreshService {
  private refreshSubject = new Subject<void>();

  constructor(private router: Router) {}

  getRefreshObservable() {
    return this.refreshSubject.asObservable();
  }

  triggerRefresh(url: string) {
    this.refreshSubject.next();
    this.router.navigate([url]);
  }
}
