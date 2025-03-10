import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Project } from './project.interface';

@Injectable({
  providedIn: 'root'
})
export class ProjectService {
  private apiUrl = 'http://localhost/api/projects';

  constructor(private http: HttpClient) {}

  getProjects(): Observable<Project[]> {
    return this.http.get<Project[]>(this.apiUrl);
  }

  createProject(name: string): Observable<Project> {
    return this.http.post<Project>(this.apiUrl, { name });
  }

  updateProject(uid: string, name: string): Observable<Project> {
    return this.http.put<Project>(`${this.apiUrl}/${uid}`, { name });
  }

  deleteProject(uid: string): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${uid}`);
  }
}
