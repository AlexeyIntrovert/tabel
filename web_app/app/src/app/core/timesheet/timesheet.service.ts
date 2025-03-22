import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface TimesheetEntry {
  id?: number;
  date: string;
  hours: number;
  projectId: number;
  groupId: number;
}

@Injectable({
  providedIn: 'root'
})
export class TimesheetService {
  private apiUrl = 'http://localhost/api/timesheet';

  constructor(private http: HttpClient) {}

  getTimesheet(): Observable<TimesheetEntry[]> {
    return this.http.get<TimesheetEntry[]>(this.apiUrl);
  }

  addEntry(entry: Omit<TimesheetEntry, 'id'>): Observable<{ id: number }> {
    return this.http.post<{ id: number }>(this.apiUrl, entry);
  }

  updateEntry(id: number, entry: Partial<TimesheetEntry>): Observable<void> {
    return this.http.put<void>(`${this.apiUrl}/${id}`, entry);
  }

  deleteEntry(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`);
  }
}
