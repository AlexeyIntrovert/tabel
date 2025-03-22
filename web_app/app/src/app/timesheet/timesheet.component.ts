import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatSelectModule } from '@angular/material/select';
import { MatSnackBar } from '@angular/material/snack-bar';
import { provideNativeDateAdapter } from '@angular/material/core';
import { TimesheetService, TimesheetEntry } from '../core/timesheet/timesheet.service';
import { DictionaryService } from '../core/staff/dictionary.service';
import { ProjectService } from '../core/project/project.service';
import { Project } from '../core/project/project.interface';
import { Group } from '../core/staff/dictionary.service';
import { forkJoin } from 'rxjs';
import { group } from '@angular/animations';

@Component({
  selector: 'app-timesheet',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatFormFieldModule,
    MatInputModule,
    MatDatepickerModule,
    MatSelectModule
  ],
  providers: [
    provideNativeDateAdapter()
  ],
  templateUrl: './timesheet.component.html',
  styleUrls: ['./timesheet.component.css']
})
export class TimesheetComponent implements OnInit {
  entries: TimesheetEntry[] = [];
  displayedColumns: string[] = ['date', 'hours', 'project', 'group', 'actions'];
  entryForm: FormGroup;
  projects: Project[] = [];
  groups: Group[] = [];
  isEditing = false;
  editingId?: number;

  constructor(
    private timesheetService: TimesheetService,
    private projectService: ProjectService,
    private dictionaryService: DictionaryService,
    private fb: FormBuilder,
    private snackBar: MatSnackBar
  ) {
    this.entryForm = this.createForm();
  }

  ngOnInit(): void {
    this.loadData();
  }

  private createForm(): FormGroup {
    return this.fb.group({
      date: [null, Validators.required],
      hours: [null, [Validators.required, Validators.min(0), Validators.max(24)]],
      projectId: [null, Validators.required],
      groupId: [null, Validators.required]
    });
  }

  private loadData(): void {
    // Load projects and groups before loading timesheet
    forkJoin({
      projects: this.projectService.getProjects(),
      groups: this.dictionaryService.getGroups()
    }).subscribe({
      next: (result) => {
        this.projects = result.projects;
        this.groups = result.groups;
        console.log('Loaded projects:', this.projects); // Отладочный вывод
        console.log('Loaded groups:', this.groups); // Отладочный вывод
        this.loadTimesheet();
      },
      error: (error) => {
        console.error('Error loading data:', error);
        this.showMessage('Ошибка загрузки данных');
      }
    });
  }

  getProjectName(projectId: number): string {
    const project = this.projects.find(p => p.id === projectId);
    console.log('Getting project name for id:', projectId, 'Found:', project); // Отладочный вывод
    //return project?.name || '';
    return this.projects.find(p => p.id === projectId)?.name || '';
  }

  getGroupName(groupId: number): string {
    console.log('Getting group name for id:', groupId, 'Found:', group); // Отладочный вывод
    return this.groups.find(g => g.id === groupId)?.name || '';
  }

  resetForm(): void {
    this.isEditing = false;
    this.editingId = undefined;
    this.entryForm.reset();
  }

  private loadTimesheet(): void {
    this.timesheetService.getTimesheet().subscribe(entries => {
      this.entries = entries;
    });
  }

  onSubmit(): void {
    if (this.entryForm.valid) {
      const formValue = this.entryForm.getRawValue();
      const submitData = {
        date: formValue.date,
        hours: formValue.hours,
        projectId: formValue.projectId,
        groupId: formValue.groupId
      };
      console.log('Submitting data:', submitData); // Отладочный вывод

      if (this.isEditing && this.editingId) {
        this.timesheetService.updateEntry(this.editingId, submitData)
          .subscribe({
            next: () => {
              this.loadTimesheet();
              this.resetForm();
              this.showMessage('Запись обновлена');
            },
            error: () => this.showMessage('Ошибка при обновлении записи')
          });
      } else {
        this.timesheetService.addEntry(submitData)
          .subscribe({
            next: () => {
              this.loadTimesheet();
              this.resetForm();
              this.showMessage('Запись добавлена');
            },
            error: () => this.showMessage('Ошибка при добавлении записи')
          });
      }
    }
  }

  editEntry(entry: TimesheetEntry): void {
    this.isEditing = true;
    this.editingId = entry.id;
    this.entryForm.patchValue({
      date: new Date(entry.date),
      hours: entry.hours,
      projectId: Number(entry.projectId),
      groupId: Number(entry.groupId)
    });
  }

  deleteEntry(id: number): void {
    if (confirm('Вы уверены, что хотите удалить эту запись?')) {
      this.timesheetService.deleteEntry(id)
        .subscribe({
          next: () => {
            this.loadTimesheet();
            this.showMessage('Запись удалена');
          },
          error: () => this.showMessage('Ошибка при удалении записи')
        });
    }
  }

  private showMessage(message: string): void {
    this.snackBar.open(message, 'Закрыть', {
      duration: 3000,
      horizontalPosition: 'end',
      verticalPosition: 'top'
    });
  }
}
