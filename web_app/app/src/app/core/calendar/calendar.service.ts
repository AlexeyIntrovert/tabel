import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Calendar } from './calendar.interface';

@Injectable({
  providedIn: 'root'
})
export class CalendarService {
  private apiUrl = 'http://localhost/api';

  constructor(private http: HttpClient) {}

  getCalendarDays(year: number, month: number): Observable<Calendar[]> {
    return this.http.get<Calendar[]>(`${this.apiUrl}/calendar/days`, {
      params: { year: year.toString(), month: month.toString() }
    });
  }
}
