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
./occ tfs:history --user <userId>
```

### Create a new entry to an item by `itemId`

```
./occ tfs:create --related <itemId>
```

### Share an item, based on `itemId` and recipient `singleId`

```
./occ tfs:share <itemId> <singleId> [--initiator]
```

`--initiator` can be used to emulate the origin of the creation of the share using `singleId`

### Get history of different events triggered on items and shares

```
./occ tfs:history [--clean] [--live]
```

`--clean` will empty the history  
`--live` will open a live feed for future events

### Display the items available from singleId's point of view

```
./occ tfs:read <singleId>
```

- Uninstall the app

```
./occ tfs:uninstall
```


