import React from 'react';
import Grow from '@material-ui/core/Grow';
import TextField from '@material-ui/core/TextField';
import SearchIcon from '@material-ui/icons/Search';
import IconButton from '@material-ui/core/IconButton';
import ClearIcon from '@material-ui/icons/Clear';
import { makeStyles } from '@material-ui/core/styles';

const useStyles = makeStyles(
    theme => ({
        main: {
            display: 'flex',
            flex: '1 0 auto',
        },
        searchIcon: {
            color: theme.palette.text.secondary,
            marginTop: '10px',
            marginRight: '8px',
        },
        searchText: {
            flex: '0.8 0',
        },
        clearIcon: {
            '&:hover': {
                color: theme.palette.error.main,
            },
        },
    }),
    { name: 'MUIDataTableSearch' },
);

const DebouncedTableSearch = ({ options, searchText, onSearch, onHide }) => {

    const classes = useStyles();

    //Override constructor
    constructor(props) {
        super(props);
        this.state = {
            text: props.searchText
        };
    };


    const handleTextChange = event => {
        onSearch(event.target.value);
    };

    const onKeyDown = event => {
        if (event.key === 'Escape') {
            onHide();
        }
    };
    //It logic that handle value of the searchText
    let value = this.state.text;
    if (searchText && searchText.value !== undefined) {
        value = searchText.value;
    };
    return (
        <Grow appear in={true} timeout={300}>
            <div className={classes.main}>
                <SearchIcon className={classes.searchIcon} />
                <TextField
                    className={classes.searchText}
                    autoFocus={true}
                    InputProps={{
                        'data-test-id': options.textLabels.toolbar.search,
                    }}
                    inputProps={{
                        'aria-label': options.textLabels.toolbar.search,
                    }}
                    value={value || ''}
                    onKeyDown={onKeyDown}
                    onChange={handleTextChange}
                    fullWidth={true}
                    placeholder={options.searchPlaceholder}
                    {...(options.searchProps ? options.searchProps : {})}
                />
                <IconButton className={classes.clearIcon} onClick={onHide}>
                    <ClearIcon />
                </IconButton>
            </div>
        </Grow>
    );
};

export default DebouncedTableSearch;