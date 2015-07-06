#include <stdio.h>
int binSearch(int a[], int min, int max, int f) {
    if (min > max) {
        return -1;
    }
    int mid = (min + max)/2;
    if (a[mid] < f) {
        return binSearch(a, min, mid-1, f);
    } else if(a[mid] > f) {
        return binSearch(a, mid+1, max, f);
    } else {
        return mid;
    }
}

int binSearchNormal(int a[], int len, int f) {
    int min = 0;
    int max = len -1;
    while (min <= max) {
        int mid = (min + max)/2;
        if (a[mid] > f) {
            min = mid + 1;
        } else if (a[mid] < f) {
            max = mid - 1;
        } else {
            return mid;
        }
    }
    return -1;
}

int main() {
    int a[] = {9,8,7,6,5,4,3,2,1};
    int f = 2;
    //int index = binSearch(a, 0, sizeof(a)/sizeof(int)-1, f);
    int index = binSearchNormal(a, sizeof(a)/sizeof(int), f);
    printf("index: %d\n", index);
    return 0;
}
