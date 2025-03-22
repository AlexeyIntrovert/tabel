import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Group {
  id: number;
  name: string;
  code: string;
}

export interface ProductionType {
  id: number;
  name: string;
}

@Injectable({
  providedIn: 'root'
})
export class DictionaryService {
  private apiUrl = 'http://localhost/api/dictionary';

  constructor(private http: HttpClient) {}

  getGroups(): Observable<Group[]> {
    return this.http.get<Group[]>(`${this.apiUrl}/groups`);
  }

  getProductionTypes(): Observable<ProductionType[]> {
    return this.http.get<ProductionType[]>(`${this.apiUrl}/production-types`);
  }
}
