import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ProfileService } from '../../core/staff/profile.service';
import { DictionaryService, Group, ProductionType } from '../../core/staff/dictionary.service';
import { UserProfile } from '../../core/staff/profile.interface';
import { Observable, of, forkJoin } from 'rxjs';
import { catchError, tap } from 'rxjs/operators';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule,
    MatIconModule,
    MatProgressSpinnerModule
  ],
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {
  loading = true;
  isEditing = false;
  userProfile?: UserProfile;
  profileForm: FormGroup;
  groups: Group[] = [];
  productionTypes: ProductionType[] = [];

  constructor(
    private profileService: ProfileService,
    private dictionaryService: DictionaryService,
    private fb: FormBuilder,
    private snackBar: MatSnackBar
  ) {
    this.profileForm = this.createForm();
  }

  ngOnInit(): void {
    forkJoin({
      groups: this.loadGroups(),
      productionTypes: this.loadProductionTypes()
    }).pipe(
      tap(result => {
        if (result.groups && result.productionTypes) {
          this.loadProfile();
        }
      })
    ).subscribe();
  }

  private createForm(): FormGroup {
    return this.fb.group({
      fullName: [{value: '', disabled: true}],
      tabNum: [{value: null, disabled: true}],
      group: [{value: null, disabled: true}],
      position: [{value: '', disabled: true}],
      grade: [{value: null, disabled: true}],
      productionType: [{value: null, disabled: true}]
    });
  }

  private loadProfile(): void {
    this.loading = true;
    this.profileService.getUserProfile().subscribe({
      next: (profile) => {
        this.userProfile = profile;
        
        if (this.groups.length && this.productionTypes.length) {
          // Fix type comparisons by comparing with numbers
          const selectedGroup = this.groups.find(g => g.id === Number(profile.group));
          const selectedProductionType = this.productionTypes.find(t => t.id === Number(profile.productionType));

          const currentValues = this.profileForm.getRawValue();
          
          this.profileForm.patchValue({
            fullName: profile.fullName ?? currentValues.fullName,
            tabNum: profile.tabNum ?? currentValues.tabNum,
            group: selectedGroup ?? currentValues.group,
            position: profile.position ?? currentValues.position,
            grade: profile.grade ?? currentValues.grade,
            productionType: selectedProductionType ?? currentValues.productionType
          });
        }
        this.loading = false;
      },
      error: (error: any) => {
        console.error('Error loading profile:', error);
        this.loading = false;
        this.showMessage('Ошибка загрузки профиля');
      }
    });
  }

  private loadGroups(): Observable<Group[] | null> {
    return this.dictionaryService.getGroups().pipe(
      tap((groups: Group[]) => {
        this.groups = groups || [];
      }),
      catchError((error: any) => {
        console.error('Error loading groups:', error);
        this.showMessage('Ошибка загрузки списка групп');
        return of(null);
      })
    );
  }

  private loadProductionTypes(): Observable<ProductionType[] | null> {
    return this.dictionaryService.getProductionTypes().pipe(
      tap((types: ProductionType[]) => {
        this.productionTypes = types || [];
      }),
      catchError((error: any) => {
        console.error('Error loading production types:', error);
        this.showMessage('Ошибка загрузки типов производства');
        return of(null);
      })
    );
  }

  toggleEdit(): void {
    this.isEditing = true;
    Object.keys(this.profileForm.controls).forEach(key => {
      const control = this.profileForm.get(key);
      if (control) {
        control.enable();
      }
    });
  }

  cancelEdit(): void {
    this.isEditing = false;
    Object.keys(this.profileForm.controls).forEach(key => {
      const control = this.profileForm.get(key);
      if (control) {
        control.disable();
      }
    });
    
    if (this.userProfile) {
      // Fix type checking by adding proper null checks and type assertions
      const selectedGroup = this.groups.find(g => 
        this.userProfile?.group && g.id === this.userProfile.group.id
      );
      const selectedProductionType = this.productionTypes.find(t => 
        this.userProfile?.productionType && t.id === this.userProfile.productionType.id
      );

      this.profileForm.patchValue({
        fullName: this.userProfile.fullName,
        tabNum: this.userProfile.tabNum,
        group: selectedGroup,
        position: this.userProfile.position,
        grade: this.userProfile.grade,
        productionType: selectedProductionType
      });
    }
  }

  saveProfile(): void {
    if (this.profileForm.valid) {
      this.loading = true;
      const formValue = this.profileForm.value;
      
      const updateData = {
        fullName: formValue.fullName,
        tabNum: formValue.tabNum,
        position: formValue.position,
        grade: formValue.grade,
        group: formValue.group?.id,
        productionType: formValue.productionType?.id
      };

      this.profileService.updateProfile(updateData).subscribe({
        next: () => {
          this.loading = false;
          this.isEditing = false;
          this.loadProfile();
          this.showMessage('Профиль успешно обновлен');
        },
        error: (error) => {
          console.error('Error updating profile:', error);
          this.loading = false;
          this.showMessage('Ошибка обновления профиля');
        }
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
