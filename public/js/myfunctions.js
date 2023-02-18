
var tableObjectStore;

function enable(ids)
{
  ids.forEach(function(id){
    $(id).prop('disabled', false);
  })
}

function disable(ids)
{
  ids.forEach(function(id){
    $(id).prop('disabled', true);
  })
}

function show(ids)
{
  ids.forEach(function(id){
    $(id).show();
  })
}

function hide(ids)
{
  ids.forEach(function(id){
    $(id).hide();
  })
}

class IndexedDB {
  constructor(dbName, storeName) {
    this.dbName = dbName;
    this.storeName = storeName;
    this.request = window.indexedDB.open(dbName, 1);

    this.request.onsuccess = (event) => {
      this.db = event.target.result;
    };

    this.request.onerror = (event) => {
      console.error(`[IndexedDB error]: ${event.target.error}`);
    };

    this.request.onupgradeneeded = (event) => {
      const db = event.target.result;
      db.createObjectStore(storeName, { keyPath: 'id', autoIncrement: true });
    };
  }

  async deleteObjectStore() {
    try {
      const transaction = this.db.transaction([this.storeName], 'versionchange');
      transaction.objectStore(this.storeName).deleteObjectStore();
      return true;
    } catch (error) {
      return error;
    }
  }

  async addData(data) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeName], 'readwrite');
      const objectStore = transaction.objectStore(this.storeName);
      const request = objectStore.add(data);

      request.onsuccess = (event) => {
        resolve(event.target.result);
      };

      request.onerror = (event) => {
        reject(`[IndexedDB error]: ${event.target.error}`);
      };
    });
  }

  async getData(key) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeName], 'readonly');
      const objectStore = transaction.objectStore(this.storeName);
      const request = objectStore.get(key);

      request.onsuccess = (event) => {
        resolve(event.target.result);
      };

      request.onerror = (event) => {
        reject(`[IndexedDB error]: ${event.target.error}`);
      };
    });
  }

  async updateData(data) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeName], 'readwrite');
      const objectStore = transaction.objectStore(this.storeName);
      const request = objectStore.put(data);

      request.onsuccess = (event) => {
        resolve(event.target.result);
      };

      request.onerror = (event) => {
        reject(`[IndexedDB error]: ${event.target.error}`);
      };
    });
  }

  async deleteData(key) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeName], 'readwrite');
      const objectStore = transaction.objectStore(this.storeName);
      const request = objectStore.delete(key);

      request.onsuccess = (event) => {
        resolve();
      };

      request.onerror = (event) => {
        reject(`[IndexedDB error]: ${event.target.error}`);
      };
    });
  }
  getAllData() {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeName], 'readonly');
      const objectStore = transaction.objectStore(this.storeName);
      const request = objectStore.openCursor();
      const data = [];

      request.onsuccess = (event) => {
        const cursor = event.target.result;
        if (cursor) {
          data.push(cursor.value);
          cursor.continue();
        } else {
          resolve(data);
        }
      };

      request.onerror = (event) => {
        reject(`[IndexedDB error]: ${event.target.error}`);
      };
    });
  }
}
