# Testing Federated Sync

This app is used to test and understand how to integrate Federated Shares into a Nextcloud App.

### Building, Installation

Copy the files in `<your_nextcloud>/apps/`
Run

```
./occ app:enable test_federated_sync
```

# Commands

### Create a new item, owned by `userId`

```
./occ tfs:create --user <userId>
```

### Create a new (sub) entry to an item by `itemId`

```
./occ tfs:create --related <itemId>
```

### Share an item, based on `itemId`, recipient `singleId` and initiator `userId`

```
./occ tfs:share <itemId> <singleId> <userId>
```

### Display the items available from `userId`'s point of view

```
./occ tfs:read <userId>
```

- Uninstall the app

```
./occ tfs:uninstall
```


