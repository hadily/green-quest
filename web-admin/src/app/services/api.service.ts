import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';


@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = 'http://localhost:8000/api'

  constructor(private http: HttpClient) {}

  /** GET */
  
  getAllUsers(): Observable<any> {
    const url = `${this.apiUrl}/user/all`; // Full URL
    return this.http.get(url).pipe(
      tap(data => console.log('Fetched all users:', data)) // Log the response
    );
  }

  getUserById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/user/${id}`);
  }

  getAllPartners(): Observable<any> {
    const url = `${this.apiUrl}/partner/`;
    return this.http.get<any[]>(url);
  }

  getPartnerById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/partner/${id}`);
  }

  getAllClients(): Observable<any> {
    const url = `${this.apiUrl}/client/`;
    return this.http.get<any[]>(url);
  }

  getClientById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/client/${id}`);
  }

  getAllAdmins(): Observable<any> {
    const url = `${this.apiUrl}/admin/`;
    return this.http.get<any[]>(url);
  }

  getAdminById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/admin/${id}`);
  }

  getAllArticles(): Observable<any> {
    const url = `${this.apiUrl}/article/`;
    return this.http.get<any[]>(url);
  }

  getArticleById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/article/${id}`);
  }

  getAllComplaints(): Observable<any> {
    const url = `${this.apiUrl}/complaints/`;
    return this.http.get<any[]>(url);
  }

  getClientComplaints(): Observable<any> {
    const url = `${this.apiUrl}/complaints/client-complaints/`;
    return this.http.get<any[]>(url);
  }

  getPartnerComplaints(): Observable<any> {
    const url = `${this.apiUrl}/complaints/partner-complaints/`;
    return this.http.get<any[]>(url);
  }

  getComplaintById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/complaints/${id}`);
  }

  getUsers(): Observable<any> {
    const token = localStorage.getItem('token');
    console.log('token: ', token);
    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);

    return this.http.get(`${this.apiUrl}/user/`, { headers });
  }

  getEvents(): Observable<any> {
    const url = `${this.apiUrl}/event/`;
    return this.http.get<any[]>(url);
  }

  getEventById(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/event/${id}`);
  }

  /** CREATE */

  createPartner(partner: any, fileName: any): Observable<any> {
    const formData: FormData = new FormData();
    formData.append('email', partner.email);
    formData.append('password', partner.password);
    formData.append('firstName', partner.firstName);
    formData.append('lastName', partner.lastName);
    formData.append('phoneNumber', partner.phoneNumber);
    formData.append('companyName', partner.companyName);
    formData.append('companyDescription', partner.companyDescription);
    formData.append('localisation', partner.localisation);
    formData.append('adminId', partner.adminId);
    formData.append('imageFilename', fileName);
    return this.http.post<FormData>(`${this.apiUrl}/partner/`, formData);
  }

  
  createClient(client: any, fileName: any): Observable<any> {
    const formData: FormData = new FormData();
    formData.append('email', client.email);
    formData.append('password', client.password);
    formData.append('firstName', client.firstName);
    formData.append('lastName', client.lastName);
    formData.append('phoneNumber', client.phoneNumber);
    formData.append('localisation', client.localisation);
    formData.append('adminId', client.adminId);
    formData.append('imageFilename', fileName);
    return this.http.post<FormData>(`${this.apiUrl}/client/`, formData);
  }

  createAdmin(admin: any, fileName: File): Observable<any> {
    const formData: FormData = new FormData();
    formData.append('email', admin.email);
    formData.append('password', admin.password);
    formData.append('firstName', admin.firstName);
    formData.append('lastName', admin.lastName);
    formData.append('phoneNumber', admin.phoneNumber);
    formData.append('imageFilename', fileName);
    return this.http.post<any>(`${this.apiUrl}/admin/`, formData);
  }

  createArticle(article: any, fileName: File): Observable<any> {
    const formData: FormData = new FormData();
    formData.append('title', article.title);
    formData.append('subTitle', article.subTitle);
    formData.append('summary', article.summary);
    formData.append('text', article.text);
    formData.append('date', article.date);
    formData.append('writerId', article.writerId);
    formData.append('imageFilename', fileName);
    return this.http.post<any>(`${this.apiUrl}/article/`, formData);
  }

  createEvent(event: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/event/`, event);
  }

  /** UPDATE */

  updatePartner(id: number, partnerData: any, file: File): Observable<any> {
    const formData = new FormData();
    formData.append('email', partnerData.email);
    formData.append('firstName', partnerData.firstName);
    formData.append('lastName', partnerData.lastName);
    formData.append('phoneNumber', partnerData.phoneNumber);
    formData.append('localisation', partnerData.localisation);
    formData.append('companyName', partnerData.companyName);
    formData.append('companyDescription', partnerData.companyDescription);
    formData.append('imageFilename', file, file.name);

    return this.http.post<any>(`${this.apiUrl}/partner/${id}`, formData);
  }

  updateClient(id: number, clientData: any, file: File): Observable<any> {
    const formData = new FormData();
    formData.append('email', clientData.email);
    formData.append('firstName', clientData.firstName);
    formData.append('lastName', clientData.lastName);
    formData.append('phoneNumber', clientData.phoneNumber);
    formData.append('localisation', clientData.localisation);
    formData.append('imageFilename', file, file.name);

    return this.http.post(`${this.apiUrl}/client/${id}`, formData);
  }

  updateAdmin(id: number, adminData: any, file: File): Observable<any> {
    const formData = new FormData();
    formData.append('email', adminData.email);
    formData.append('firstName', adminData.firstName);
    formData.append('lastName', adminData.lastName);
    formData.append('phoneNumber', adminData.phoneNumber);
    formData.append('imageFilename', file, file.name);
    return this.http.post<any>(`${this.apiUrl}/admin/${id}`, formData);
  }

  updateArticle(id: number, article: any, file: File): Observable<any> {
    const formData = new FormData();
    formData.append('title', article.title);
    formData.append('subTitle', article.subTitle);
    formData.append('summary', article.summary);
    formData.append('text', article.text);
    formData.append('date', article.date);
    formData.append('imageFilename', file);
    return this.http.post<any>(`${this.apiUrl}/article/${id}`, formData);
  }

  updateComplaints(id: number, complaint: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/complaints/${id}`, complaint);
  }

  updateUser(id: number, user: any, file: File): Observable<any> {
    const formData = new FormData();
    formData.append('email', user.email);
    formData.append('firstName', user.firstName);
    formData.append('lastName', user.lastName);
    formData.append('phoneNumber', user.phoneNumber);
    formData.append('imageFilename', file, file.name);
    return this.http.post<any>(`${this.apiUrl}/user/${id}`, formData);
  }

  updateEvent(id: number, event: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/event/${id}`, event);
  }

  /** DELETE */

  deletePartner(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/partner/${id}`);
  }

  deleteClient(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/client/${id}`);
  }

  deleteAdmin(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/admin/${id}`);
  }

  deleteArticle(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/article/${id}`);
  }

  deleteEvent(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/event/${id}`);
  }


  /** SEARCH */

  searchPartners(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/partner/search`, { params });
  }

  searchClients(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/client/search`, { params });
  }

  searchAdmins(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/admin/search`, { params });
  }

  searchArticles(query: string): Observable<any[]> {
    const params = new HttpParams().set('query', query);
    return this.http.get<any[]>(`${this.apiUrl}/article/search`, { params });
  }

  /** CHANGE PASSWORD */
  resetPassword(id: number, data: { currentPassword: string; newPassword: string }): Observable<any> {
    return this.http.put(`${this.apiUrl}/user/reset-password/${id}`, data);
  }

  
  
}
