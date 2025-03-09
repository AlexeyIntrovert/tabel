import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTableModule } from '@angular/material/table';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { CalendarService } from '../core/calendar/calendar.service';
import { Calendar, Month } from '../core/calendar/calendar.interface';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-calendar',
  standalone: true,
  imports: [CommonModule, MatTableModule, MatProgressSpinnerModule],
  templateUrl: './calendar.component.html',
  styleUrls: ['./calendar.component.css']
})
export class CalendarComponent implements OnInit {
  isLoading = true;
  currentYear = new Date().getFullYear();
  days = Array.from({length: 31}, (_, i) => i + 1);
  months: Month[] = [
    { id: 1, name: 'Январь', days: [] },
    { id: 2, name: 'Февраль', days: [] },
    { id: 3, name: 'Март', days: [] },
    { id: 4, name: 'Апрель', days: [] },
    { id: 5, name: 'Май', days: [] },
    { id: 6, name: 'Июнь', days: [] },
    { id: 7, name: 'Июль', days: [] },
    { id: 8, name: 'Август', days: [] },
    { id: 9, name: 'Сентябрь', days: [] },
    { id: 10, name: 'Октябрь', days: [] },
    { id: 11, name: 'Ноябрь', days: [] },
    { id: 12, name: 'Декабрь', days: [] }
  ];

  constructor(private calendarService: CalendarService) {}

  ngOnInit() {
    this.loadCalendarData();
  }

  loadCalendarData() {
    const requests = this.months.map(month =>
      this.calendarService.getCalendarDays(this.currentYear, month.id)
    );

    forkJoin(requests).subscribe({
      next: (results) => {
        results.forEach((days, index) => {
          this.months[index].days = this.generateMonthDays(days, this.months[index].id);
        });
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error loading calendar data:', error);
        this.isLoading = false;
      }
    });
  }

  generateMonthDays(days: Calendar[], monthId: number): Calendar[] {
    const monthDays: Calendar[] = [];
    const daysInMonth = new Date(this.currentYear, monthId - 1, 0).getDate();
    
    for (let i = 1; i <= daysInMonth; i++) {
      const day = days.find(d => new Date(d.date).getDate() === i);
      monthDays.push(day || { 
        date: `${this.currentYear}-${monthId.toString().padStart(2, '0')}-${i.toString().padStart(2, '0')}`,
        type: 'W',
        hours: 8 
      });
    }
    
    return monthDays;
  }

  getDayClass(day: Calendar): string {
    if (!day) return '';
    const classes: Record<string, string> = {
      'W': 'day-work',
      'P': 'day-pre-holiday',
      'H': 'day-holiday',
      'R': 'day-rest'
    };
    return classes[day.type] || '';
  }

  getDayContent(day: Calendar): string {
    if (!day) return '';
    const content: Record<string, string> = {
      'W': '8',
      'P': '7',
      'H': 'П',
      'R': 'В'
    };
    return content[day.type] || '';
  }
}
