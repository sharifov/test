function Groups(props) {
    const items = [];
    let data = Object.assign({}, props);

    const externalKey = 'external';
    if (typeof data.groups[externalKey] !== 'undefined') {
        items.push(<GroupItem key={externalKey} group={{'calls': data.groups[externalKey].calls, 'project': '', 'department': 'External Contacts'}}/>);
        delete data.groups[externalKey];
    }

    const internalKey = 'internal';
    if (typeof data.groups[internalKey] !== 'undefined') {
        items.push(<GroupItem key={internalKey} group={{'calls': data.groups[internalKey].calls, 'project': '', 'department': 'Internal Contacts'}}/>);
        delete data.groups[internalKey];
    }

    for (let key in data.groups) {
        if (key === 'inArray') {
            continue;
        }
        items.push(<GroupItem key={key} group={data.groups[key]}/>);
    }

    return (
        <ul className="calls-separator">
            {items}
        </ul>
    );
}
